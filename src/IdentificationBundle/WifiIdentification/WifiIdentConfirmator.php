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
use IdentificationBundle\Identification\Exception\MissingIdentificationDataException;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeVerifier;
use IdentificationBundle\WifiIdentification\Common\RequestProvider;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider;
use IdentificationBundle\WifiIdentification\Service\IdentFinisher;
use IdentificationBundle\WifiIdentification\Service\MsisdnCleaner;

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
     * WifiIdentConfirmator constructor.
     * @param WifiIdentificationHandlerProvider $handlerProvider
     * @param PinCodeVerifier                   $codeVerifier
     * @param CarrierRepositoryInterface        $carrierRepository
     * @param PinVerifyProcess                  $pinVerifyProcess
     * @param RequestProvider                   $requestProvider
     * @param MsisdnCleaner                     $msisdnCleaner
     * @param IdentificationDataStorage         $dataStorage
     * @param IdentFinisher                     $identFinisher
     */
    public function __construct(
        WifiIdentificationHandlerProvider $handlerProvider,
        PinCodeVerifier $codeVerifier,
        CarrierRepositoryInterface $carrierRepository,
        PinVerifyProcess $pinVerifyProcess,
        RequestProvider $requestProvider,
        MsisdnCleaner $msisdnCleaner,
        IdentificationDataStorage $dataStorage,
        IdentFinisher $identFinisher
    )
    {
        $this->handlerProvider   = $handlerProvider;
        $this->codeVerifier      = $codeVerifier;
        $this->carrierRepository = $carrierRepository;
        $this->pinVerifyProcess  = $pinVerifyProcess;
        $this->requestProvider   = $requestProvider;
        $this->msisdnCleaner     = $msisdnCleaner;
        $this->dataStorage       = $dataStorage;
        $this->identFinisher     = $identFinisher;
    }

    public function confirm(int $carrierId, string $pinCode, string $mobileNumber, string $ip): bool
    {
        /** @var PinRequestResult $pinRequestResult */
        $pinRequestResult = $this->dataStorage->readPreviousOperationResult('pinRequest');
        if (!$pinRequestResult) {
            throw new MissingIdentificationDataException('pinRequest data is missing');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->handlerProvider->get($carrier);

        if (!$pinRequestResult->isNeedVerifyRequest()) {
            return $this->codeVerifier->verifyPinCode($pinCode);
        }

        if ($handler instanceof HasCustomPinVerifyRules) {
            $additionalParams = $handler->getAdditionalPinVerifyParams($pinRequestResult);
        } else {
            $additionalParams = [];
        }

        $msisdn     = $this->msisdnCleaner->clean($mobileNumber, $carrier);
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

        } catch (PinVerifyProcessException $exception) {

            if ($handler instanceof HasCustomPinVerifyRules) {
                $handler->afterFailedPinVerify($exception);
            }
        }

        return true;
    }

}