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
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\Exception\AlreadyIdentifiedException;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Exception\MissingIdentificationDataException;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeVerifier;
use IdentificationBundle\WifiIdentification\Common\RequestProvider;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider;
use IdentificationBundle\WifiIdentification\Service\IdentFinisher;
use IdentificationBundle\WifiIdentification\Service\MsisdnCleaner;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;

class WifiIdentConfirmator
{
    use ResponseTrait;

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
     * @var PostPaidHandler
     */
    private $postPaidHandler;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * WifiIdentConfirmator constructor.
     *
     * @param WifiIdentificationHandlerProvider $handlerProvider
     * @param PinCodeVerifier $codeVerifier
     * @param CarrierRepositoryInterface $carrierRepository
     * @param PinVerifyProcess $pinVerifyProcess
     * @param RequestProvider $requestProvider
     * @param MsisdnCleaner $msisdnCleaner
     * @param IdentificationDataStorage $dataStorage
     * @param IdentFinisher $identFinisher
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserRepository $userRepository
     * @param PostPaidHandler $postPaidHandler
     * @param RouterInterface $router
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
        UserRepository $userRepository,
        PostPaidHandler $postPaidHandler,
        RouterInterface $router
    ) {
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
        $this->postPaidHandler        = $postPaidHandler;
        $this->router                 = $router;
    }

    /**
     * @param int $carrierId
     * @param string $pinCode
     * @param string $mobileNumber
     * @param string $ip
     *
     * @return JsonResponse
     *
     * @throws AlreadyIdentifiedException
     * @throws MissingIdentificationDataException
     * @throws FailedIdentificationException
     * @throws \Exception
     */
    public function confirm(int $carrierId, string $pinCode, string $mobileNumber, string $ip): JsonResponse
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->handlerProvider->get($carrier);

        /** @var PinRequestResult $pinRequestResult */
        $pinRequestResult = $this->dataStorage->readPreviousOperationResult('pinRequest');
        if (!$pinRequestResult) {
            throw new MissingIdentificationDataException('pinRequest data is missing');
        }

        $successResponse = $this->getSimpleJsonResponse('Confirmed', 200, [], [
            'success'     => true,
            'redirectUrl' => $handler->getRedirectUrl() ?? $this->router->generate('subscription.subscribe')
        ]);

        $msisdn = $this->msisdnCleaner->clean($mobileNumber, $carrier);

        if (!$pinRequestResult->isNeedVerifyRequest()) {
            $isValid = $this->codeVerifier->verifyPinCode($pinCode);
            if (!$isValid) {
                throw new FailedIdentificationException('You have entered a wrong PIN, please try again or re-send a new');
            }

            $user = $handler->getExistingUser($msisdn);
            if ($user) {
                $this->identFinisher->finishForExistingUser($user, $msisdn, $ip);
            } else {
                $this->identFinisher->finish($msisdn, $carrier, $ip);
            }

            $this->dataStorage->cleanPreviousOperationResult('pinRequest');

            if ($handler instanceof HasPostPaidRestriction) {
                $this->postPaidHandler->process($msisdn, $carrier->getBillingCarrierId());
            }
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
            } catch (PinVerifyProcessException $exception) {
                if ($handler instanceof HasCustomPinVerifyRules) {
                    $handler->afterFailedPinVerify($exception);
                }
                throw $exception;
            }

            if ($handler instanceof HasCustomPinVerifyRules) {
                $handler->afterSuccessfulPinVerify($result);
                $finalMsisdn = $handler->getMsisdnFromResult($result, $msisdn);
            } else {
                $finalMsisdn = $msisdn;
            }

            $user = $handler->getExistingUser($finalMsisdn);
            if ($user) {
                $this->identFinisher->finishForExistingUser($user, $msisdn, $ip);
            } else {
                $this->identFinisher->finish($finalMsisdn, $carrier, $ip);
            }

            $this->dataStorage->cleanPreviousOperationResult('pinRequest');

            if ($handler instanceof HasPostPaidRestriction) {
                $this->postPaidHandler->process($finalMsisdn, $carrier->getBillingCarrierId());
            }
        }

        return $successResponse;
    }
}