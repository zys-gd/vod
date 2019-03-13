<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:58
 */

namespace IdentificationBundle\WifiIdentification;


use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinVerifyProcessException;
use IdentificationBundle\BillingFramework\Process\PinVerifyProcess;
use IdentificationBundle\Identification\Exception\AlreadyIdentifiedException;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Exception\MissingIdentificationDataException;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeVerifier;
use IdentificationBundle\WifiIdentification\Common\RequestProvider;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider;
use IdentificationBundle\WifiIdentification\Service\IdentFinisher;
use IdentificationBundle\WifiIdentification\Service\MsisdnCleaner;
use SubscriptionBundle\Repository\SubscriptionRepository;

class WifiIdentConfirmator
{
    /**
     * @var WifiIdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var PinCodeVerifier
     */
    private $codeVerifier;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var PinVerifyProcess
     */
    private $pinVerifyProcess;
    /**
     * @var RequestProvider
     */
    private $requestProvider;
    /**
     * @var MsisdnCleaner
     */
    private $msisdnCleaner;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var IdentFinisher
     */
    private $identFinisher;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * WifiIdentConfirmator constructor.
     * @param WifiIdentificationHandlerProvider $handlerProvider
     * @param PinCodeVerifier                   $codeVerifier
     * @param CarrierRepositoryInterface        $carrierRepository
     * @param PinVerifyProcess                  $pinVerifyProcess
     * @param RequestProvider                   $requestProvider
     * @param MsisdnCleaner                     $msisdnCleaner
     * @param IdentificationDataStorage         $dataStorage
     * @param IdentFinisher                     $identFinisher
     * @param SubscriptionRepository            $subscriptionRepository
     * @param UserRepository                    $userRepository
     */
    public function __construct(
        WifiIdentificationHandlerProvider $handlerProvider,
        PinCodeVerifier $codeVerifier,
        CarrierRepositoryInterface $carrierRepository,
        PinVerifyProcess $pinVerifyProcess,
        RequestProvider $requestProvider,
        MsisdnCleaner $msisdnCleaner,
        IdentificationDataStorage $dataStorage,
        IdentFinisher $identFinisher,
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository
    )
    {
        $this->handlerProvider        = $handlerProvider;
        $this->codeVerifier           = $codeVerifier;
        $this->carrierRepository      = $carrierRepository;
        $this->pinVerifyProcess       = $pinVerifyProcess;
        $this->requestProvider        = $requestProvider;
        $this->msisdnCleaner          = $msisdnCleaner;
        $this->dataStorage            = $dataStorage;
        $this->identFinisher          = $identFinisher;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository         = $userRepository;
    }

    /**
     * @param int    $carrierId
     * @param string $pinCode
     * @param string $mobileNumber
     * @param string $ip
     * @throws AlreadyIdentifiedException
     * @throws MissingIdentificationDataException
     * @throws FailedIdentificationException
     */
    public function confirm(int $carrierId, string $pinCode, string $mobileNumber, string $ip)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->handlerProvider->get($carrier);

        if ($handler->getExistingUser($mobileNumber)) {
            throw new AlreadyIdentifiedException('User is already exists');
        }

        /** @var PinRequestResult $pinRequestResult */
        $pinRequestResult = $this->dataStorage->readPreviousOperationResult('pinRequest');
        if (!$pinRequestResult) {
            throw new MissingIdentificationDataException('pinRequest data is missing');
        }

        $msisdn = $this->msisdnCleaner->clean($mobileNumber, $carrier);

        if (!$pinRequestResult->isNeedVerifyRequest()) {
            $isValid = $this->codeVerifier->verifyPinCode($pinCode);
            if (!$isValid) {
                throw new FailedIdentificationException('You have entered a wrong PIN, please try again or re-send a new');
            }

            $this->identFinisher->finish($msisdn, $carrier, $ip);
            $this->dataStorage->cleanPreviousOperationResult('pinRequest');
            return;
        } else {

            if ($handler instanceof HasCustomPinVerifyRules) {
                $additionalParams = $handler->getAdditionalPinVerifyParams($pinRequestResult);
            } else {
                $additionalParams = [];
            }

            $parameters = $this->requestProvider->getPinVerifyParameters(
                $msisdn,
                $carrier->getBillingCarrierId(),
                $carrier->getOperatorId(),
                $pinCode,
                $pinRequestResult->getUserIdentifier(),
                $additionalParams
            );

            try {
                $result = $this->pinVerifyProcess->doPinVerify($parameters);
                if ($handler instanceof HasCustomPinVerifyRules) {
                    $handler->afterSuccessfulPinVerify($result);
                }
                $this->identFinisher->finish($msisdn, $carrier, $ip);
                $this->dataStorage->cleanPreviousOperationResult('pinRequest');

            } catch (PinVerifyProcessException $exception) {
                if ($handler instanceof HasCustomPinVerifyRules) {
                    $handler->afterFailedPinVerify($exception);
                }
                throw $exception;
            }
        }

    }

}