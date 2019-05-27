<?php

namespace IdentificationBundle\WifiIdentification;

use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\BillingFramework\Process\PinRequestProcess;
use IdentificationBundle\BillingFramework\Process\PinResendProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Exception\AlreadyIdentifiedException;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeSaver;
use IdentificationBundle\WifiIdentification\Common\RequestProvider;
use IdentificationBundle\WifiIdentification\Handler\HasConsentPageFlow;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinRequestRules;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinResendRules;
use IdentificationBundle\WifiIdentification\Handler\HasInternalSMSHandling;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider;
use IdentificationBundle\WifiIdentification\Service\MessageComposer;
use IdentificationBundle\WifiIdentification\Service\MsisdnCleaner;

/**
 * Class WifiIdentSMSSender
 */
class WifiIdentSMSSender
{
    /**
     * @var WifiIdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var PinRequestProcess
     */
    private $pinRequestProcess;
    /**
     * @var MessageComposer
     */
    private $messageComposer;
    /**
     * @var MsisdnCleaner
     */
    private $cleaner;
    /**
     * @var PinCodeSaver
     */
    private $pinCodeSaver;
    /**
     * @var RequestProvider
     */
    private $requestProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PinResendProcess
     */
    private $pinResendProcess;

    /**
     * WifiIdentSMSSender constructor.
     * @param WifiIdentificationHandlerProvider $handlerProvider
     * @param CarrierRepositoryInterface        $carrierRepository
     * @param PinRequestProcess                 $pinRequestProcess
     * @param MessageComposer                   $messageComposer
     * @param MsisdnCleaner                     $cleaner
     * @param PinCodeSaver                      $pinCodeSaver
     * @param RequestProvider                   $requestProvider
     * @param IdentificationDataStorage         $dataStorage
     * @param UserRepository                    $userRepository
     * @param PinResendProcess                  $pinResendProcess
     */
    public function __construct(
        WifiIdentificationHandlerProvider $handlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        PinRequestProcess $pinRequestProcess,
        MessageComposer $messageComposer,
        MsisdnCleaner $cleaner,
        PinCodeSaver $pinCodeSaver,
        RequestProvider $requestProvider,
        IdentificationDataStorage $dataStorage,
        UserRepository $userRepository,
        PinResendProcess $pinResendProcess
    ) {
        $this->handlerProvider   = $handlerProvider;
        $this->carrierRepository = $carrierRepository;
        $this->pinRequestProcess = $pinRequestProcess;
        $this->messageComposer   = $messageComposer;
        $this->cleaner           = $cleaner;
        $this->pinCodeSaver      = $pinCodeSaver;
        $this->requestProvider   = $requestProvider;
        $this->dataStorage       = $dataStorage;
        $this->userRepository    = $userRepository;
        $this->pinResendProcess  = $pinResendProcess;
    }

    /**
     * @param int $carrierId
     * @param string $mobileNumber
     * @param bool $isResend
     */
    public function sendSMS(int $carrierId, string $mobileNumber, bool $isResend = false): void
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->handlerProvider->get($carrier);

        if ($handler instanceof HasConsentPageFlow && $handler->hasActiveSubscription($mobileNumber)) {
            throw new PinRequestProcessException('', 101, '');
        }

        if (!$handler instanceof HasConsentPageFlow && $handler->getExistingUser($mobileNumber)) {
            throw new AlreadyIdentifiedException('User is already identified');
        }

        $pinCode = '000000';
        if (!$handler->areSMSSentByBilling()) {
            $pinCodeObject = $this->pinCodeSaver->savePinCode(mt_rand(0, 99999));
            $pinCode       = $pinCodeObject->getPin();
        }

        $msisdn = $this->cleaner->clean($mobileNumber, $carrier);
        $body   = $this->messageComposer->composePinCodeMessage('_subtext_', 'en', $pinCode);

        if ($isResend && $handler instanceof HasCustomPinResendRules) {
            $this->resendSMS($handler, $carrier, $body);

            return;
        }

        if ($handler instanceof HasCustomPinRequestRules) {
            $additionalParameters = $handler->getAdditionalPinRequestParams();
        } else {
            $additionalParameters = [];
        }

        $parameters = $this->requestProvider->getPinRequestParameters(
            $msisdn,
            $carrier->getBillingCarrierId(),
            $carrier->getOperatorId(),
            $body,
            $additionalParameters
        );

        try {
            $result = $this->pinRequestProcess->doPinRequest($parameters);
            $this->dataStorage->storeOperationResult('pinRequest', $result);

            if ($handler instanceof HasCustomPinRequestRules) {
                $handler->afterSuccessfulPinRequest($result);
            }

        } catch (PinRequestProcessException $exception) {

            if ($handler instanceof HasCustomPinRequestRules) {
                $handler->getPinRequestErrorMessage($exception);
            }
            throw $exception;
        }
    }

    /**
     * @param HasCustomPinResendRules $handler
     * @param CarrierInterface $carrier
     * @param string $body
     */
    public function resendSMS(HasCustomPinResendRules $handler, CarrierInterface $carrier, string $body)
    {
        $pinRequestResult = $this->dataStorage->readPreviousOperationResult('pinRequest');
        $additionalParameters = $handler->getAdditionalPinResendParameters($pinRequestResult);

        $parameters = $this->requestProvider->getPinResendParameters(
            $carrier->getBillingCarrierId(),
            $carrier->getOperatorId(),
            $body,
            $additionalParameters
        );

        try {
            $this->pinResendProcess->doPinRequest($parameters);
        } catch (PinRequestProcessException $exception) {
            throw $exception;
        }
    }
}