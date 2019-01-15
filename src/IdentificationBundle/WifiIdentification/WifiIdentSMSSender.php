<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:58
 */

namespace IdentificationBundle\WifiIdentification;


use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\BillingFramework\Process\PinRequestProcess;
use IdentificationBundle\Identification\Exception\AlreadyIdentifiedException;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeSaver;
use IdentificationBundle\WifiIdentification\Common\RequestProvider;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinRequestRules;
use IdentificationBundle\WifiIdentification\Handler\HasInternalSMSHandling;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider;
use IdentificationBundle\WifiIdentification\Service\MessageComposer;
use IdentificationBundle\WifiIdentification\Service\MsisdnCleaner;

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
        UserRepository $userRepository
    )
    {
        $this->handlerProvider   = $handlerProvider;
        $this->carrierRepository = $carrierRepository;
        $this->pinRequestProcess = $pinRequestProcess;
        $this->messageComposer   = $messageComposer;
        $this->cleaner           = $cleaner;
        $this->pinCodeSaver      = $pinCodeSaver;
        $this->requestProvider   = $requestProvider;
        $this->dataStorage       = $dataStorage;
        $this->userRepository    = $userRepository;
    }

    public function sendSMS(int $carrierId, string $mobileNumber): void
    {
        if ($this->userRepository->findOneByMsisdn($mobileNumber)) {
            throw new AlreadyIdentifiedException('User is already identified');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        $handler = $this->handlerProvider->get($carrier);

        $pinCode = '000000';
        if (!$handler->areSMSSentByBilling()) {
            $pinCodeObject = $this->pinCodeSaver->savePinCode(mt_rand(0, 99999));
            $pinCode       = $pinCodeObject->getPin();
        }

        $msisdn = $this->cleaner->clean($mobileNumber, $carrier);
        $body   = $this->messageComposer->composePinCodeMessage('_subtext_', 'en', $pinCode);

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
}