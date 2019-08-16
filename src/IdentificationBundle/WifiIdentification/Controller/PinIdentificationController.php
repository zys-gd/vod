<?php

namespace IdentificationBundle\WifiIdentification\Controller;

use ExtrasBundle\API\Controller\APIControllerInterface;
use ExtrasBundle\Controller\Traits\ResponseTrait;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\BillingFramework\Process\Exception\PinVerifyProcessException;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeResolver;
use IdentificationBundle\WifiIdentification\WifiIdentConfirmator;
use IdentificationBundle\WifiIdentification\WifiIdentSMSSender;
use IdentificationBundle\WifiIdentification\WifiPhoneOptionsProvider;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitNotifier;
use SubscriptionBundle\Subscription\Common\RouteProvider;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\CAPTool\Subscription\Exception\CapToolAccessException;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PinIdentificationController
 */
class PinIdentificationController extends AbstractController implements APIControllerInterface
{
    use ResponseTrait;
    /**
     * @var WifiIdentSMSSender
     */
    private $identSMSSender;
    /**
     * @var WifiIdentConfirmator
     */
    private $identConfirmator;
    /**
     * @var ErrorCodeResolver
     */
    private $errorCodeResolver;
    /**
     * @var SubscriptionLimiter
     */
    private $limiter;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var SubscriptionLimitNotifier
     */
    private $subscriptionLimitNotifier;

    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var WifiPhoneOptionsProvider
     */
    private $phoneOptionsProvider;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;

    /**
     * PinIdentificationController constructor
     *
     * @param WifiIdentSMSSender          $identSMSSender
     * @param WifiIdentConfirmator        $identConfirmator
     * @param ErrorCodeResolver           $errorCodeResolver
     * @param SubscriptionLimiter         $limiter
     * @param CarrierRepositoryInterface  $carrierRepository
     * @param SubscriptionLimitNotifier   $subscriptionLimitNotifier
     * @param BlacklistVoter              $blacklistVoter
     * @param BlacklistAttemptRegistrator $blacklistAttemptRegistrator
     * @param WifiPhoneOptionsProvider    $phoneOptionsProvider
     * @param RouteProvider               $routeProvider
     */
    public function __construct(
        WifiIdentSMSSender $identSMSSender,
        WifiIdentConfirmator $identConfirmator,
        ErrorCodeResolver $errorCodeResolver,
        SubscriptionLimiter $limiter,
        CarrierRepositoryInterface $carrierRepository,
        SubscriptionLimitNotifier $subscriptionLimitNotifier,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator,
        WifiPhoneOptionsProvider $phoneOptionsProvider,
        RouteProvider $routeProvider,
        LoggerInterface $logger,
        CampaignExtractor $campaignExtractor,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
    )
    {
        $this->identSMSSender              = $identSMSSender;
        $this->identConfirmator            = $identConfirmator;
        $this->errorCodeResolver           = $errorCodeResolver;
        $this->limiter                     = $limiter;
        $this->carrierRepository           = $carrierRepository;
        $this->subscriptionLimitNotifier   = $subscriptionLimitNotifier;
        $this->blacklistVoter              = $blacklistVoter;
        $this->blacklistAttemptRegistrator = $blacklistAttemptRegistrator;
        $this->phoneOptionsProvider        = $phoneOptionsProvider;
        $this->routeProvider               = $routeProvider;
    }

    /**
     * @Method("POST")
     * @Route("/pincode/send",name="send_sms_pin_code")
     *
     * @param Request $request
     * @param ISPData $ispData
     *
     * @return JsonResponse
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function sendSMSPinCodeAction(Request $request, ISPData $ispData)
    {
        if (!$mobileNumber = $request->get('mobile_number', '')) {
            throw new BadRequestHttpException('`mobile_number` is required');
        }

        try {
            $this->limiter->ensureCapIsNotReached($request->getSession());
        } catch (CapToolAccessException $exception) {
            return $this->getSimpleJsonResponse('Subscription limit has been reached', 200, [], [
                'success' => false, 'redirectUrl' => $this->routeProvider->getActionIsNotAllowedUrl()
            ]);
        }

        $billingCarrierId = $ispData->getCarrierId();
        $cleanPhoneNumber = str_replace('+', '', $mobileNumber);

        if ($this->blacklistVoter->isPhoneNumberBlacklisted($cleanPhoneNumber) ||
            $this->blacklistAttemptRegistrator->isSubscriptionAttemptRaised($cleanPhoneNumber, (int)$billingCarrierId)
        ) {
            return $this->getSimpleJsonResponse('User in black list', 200, [], [
                'success' => false, 'redirectUrl' => $this->blacklistVoter->getRedirectUrl()
            ]);
        }

        $this->limiter->reserveSlotForSubscription($request->getSession());

        $postData = $request->request->all();
        $isResend = isset($postData['resend-pin']);

        $campaign = $this->campaignExtractor->getCampaignFromSession($request->getSession());
        $isZeroCreditSubAvailable = $this->zeroCreditSubscriptionChecking->isZeroCreditAvailable($billingCarrierId, $campaign);

        try {
            $this->identSMSSender->sendSMS($billingCarrierId, $mobileNumber, $isZeroCreditSubAvailable, $isResend);
            $data = ['success' => true, 'carrierId' => $billingCarrierId, 'isResend' => $isResend];

            return $this->getSimpleJsonResponse('Sent', 200, [], $data);
        } catch (PinRequestProcessException $exception) {
            $this->logger->debug('Send pin error. Try to resolve error message', [
                'code' => $exception->getCode(),
                'carrierId' => $billingCarrierId
            ]);

            $message = $this->errorCodeResolver->resolveMessage($exception->getCode(), $billingCarrierId);
            return $this->getSimpleJsonResponse($message, 200, [], ['success' => false, 'code' => $exception->getCode()]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }

    /**
     * @Method("POST")
     * @Route("/pincode/confirm",name="confirm_sms_pin_code")
     * @param Request $request
     * @param ISPData $ispData
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function confirmSMSPinCodeAction(Request $request, ISPData $ispData)
    {
        if (!$pinCode = $request->get('pin_code', '')) {
            throw new BadRequestHttpException('`pin_code` is required');
        }

        if (!$mobileNumber = $request->get('mobile_number', '')) {
            throw new BadRequestHttpException('`mobile_number` is required');
        }

        $carrierId = $ispData->getCarrierId();
        $campaign = $this->campaignExtractor->getCampaignFromSession($request->getSession());
        $isZeroCreditSubAvailable = $this->zeroCreditSubscriptionChecking->isZeroCreditAvailable($carrierId, $campaign);

        try {
            $response = $this->identConfirmator->confirm(
                $carrierId,
                $pinCode,
                $mobileNumber,
                $request->getClientIp(),
                $isZeroCreditSubAvailable
            );

            return $response;
        } catch (PinVerifyProcessException $exception) {
            $message = $this->errorCodeResolver->resolveMessage($exception->getCode(), $carrierId);
            return $this->getSimpleJsonResponse($message, 200, [], ['success' => false]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }

    /**
     * @Method("GET")
     * @Route("/pincode/phone-options",name="wifi_phone_options")
     * @param Request $request
     * @param ISPData $ispData
     *
     * @return JsonResponse
     */
    public function getPhoneMaskOptions(Request $request, ISPData $ispData): JsonResponse
    {

        try {

            $phoneValidationOptions = $this->phoneOptionsProvider->getPhoneValidationOptions($ispData->getCarrierId());

            return $this->getSimpleJsonResponse('', 200, [], ['phoneOptions' => [
                'placeholder'       => $phoneValidationOptions->getPhonePlaceholder(),
                'phoneRegexPattern' => $phoneValidationOptions->getPhoneRegexPattern(),
                'pinRegexPattern'   => $phoneValidationOptions->getPinRegexPattern()
            ]]);

        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }
}