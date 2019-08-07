<?php

namespace IdentificationBundle\WifiIdentification\Controller;

use ExtrasBundle\API\Controller\APIControllerInterface;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\BillingFramework\Process\Exception\PinVerifyProcessException;
use IdentificationBundle\Form\LPType;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeResolver;
use IdentificationBundle\WifiIdentification\WifiIdentConfirmator;
use IdentificationBundle\WifiIdentification\WifiIdentSMSSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Service\CAPTool\Exception\CapToolAccessException;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiter;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimitNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PinIdentificationController
 */
class PinIdentificationController extends AbstractController implements APIControllerInterface
{
    use ResponseTrait;
    /**
     * @var \IdentificationBundle\WifiIdentification\WifiIdentSMSSender
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
     * @var string
     */
    private $defaultRedirectUrl;
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
     * PinIdentificationController constructor
     *
     * @param WifiIdentSMSSender          $identSMSSender
     * @param WifiIdentConfirmator        $identConfirmator
     * @param ErrorCodeResolver           $errorCodeResolver
     * @param SubscriptionLimiter         $limiter
     * @param string                      $defaultRedirectUrl
     * @param CarrierRepositoryInterface  $carrierRepository
     * @param SubscriptionLimitNotifier   $subscriptionLimitNotifier
     * @param BlacklistVoter              $blacklistVoter
     * @param BlacklistAttemptRegistrator $blacklistAttemptRegistrator
     */
    public function __construct(
        WifiIdentSMSSender $identSMSSender,
        WifiIdentConfirmator $identConfirmator,
        ErrorCodeResolver $errorCodeResolver,
        SubscriptionLimiter $limiter,
        string $defaultRedirectUrl,
        CarrierRepositoryInterface $carrierRepository,
        SubscriptionLimitNotifier $subscriptionLimitNotifier,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator
    )
    {    $this->identSMSSender              = $identSMSSender;
        $this->identConfirmator            = $identConfirmator;
        $this->errorCodeResolver           = $errorCodeResolver;
        $this->limiter                     = $limiter;
        $this->defaultRedirectUrl          = $defaultRedirectUrl;
        $this->carrierRepository           = $carrierRepository;
        $this->subscriptionLimitNotifier   = $subscriptionLimitNotifier;
        $this->blacklistVoter              = $blacklistVoter;
        $this->blacklistAttemptRegistrator = $blacklistAttemptRegistrator;
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
        $postData = $request->request->all();
        $isResend = isset($postData['resend-pin']);
        $mobileNumber = $request->get('mobile_number', '');
        $billingCarrierId = $ispData->getCarrierId();

        $form = $this->createForm(LPType::class, [],['csrf_protection' => false, 'allow_extra_fields'=> true]);
        $form->submit($postData);
        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            return $this->getSimpleJsonResponse($errors->current()->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->limiter->ensureCapIsNotReached($request->getSession());
        } catch (CapToolAccessException $exception) {
            return $this->getSimpleJsonResponse(
                'Subscription limit has been reached',
                Response::HTTP_BAD_REQUEST,
                [],
                ['redirectUrl' => $this->defaultRedirectUrl]
            );
        }

        $cleanPhoneNumber = str_replace('+', '', $mobileNumber);
        if ($this->blacklistVoter->isPhoneNumberBlacklisted($cleanPhoneNumber)
            || $this->blacklistAttemptRegistrator->isSubscriptionAttemptRaised($cleanPhoneNumber, (int) $billingCarrierId)
        ) {
            return $this->getSimpleJsonResponse(
                'User in black list',
                Response::HTTP_BAD_REQUEST,
                [],
                ['redirectUrl' => $this->blacklistVoter->getRedirectUrl()]
            );
        }

        $this->limiter->reserveSlotForSubscription($request->getSession());

        try {
            $this->identSMSSender->sendSMS($billingCarrierId, $mobileNumber, $isResend);
            $data = ['carrierId' => $billingCarrierId, 'isResend' => $isResend];

            return $this->getSimpleJsonResponse('Sent', Response::HTTP_OK, [], $data);
        } catch (PinRequestProcessException $exception) {
            $message = $this->errorCodeResolver->resolveMessage($exception->getCode(), $billingCarrierId);
            return $this->getSimpleJsonResponse($message, Response::HTTP_BAD_REQUEST, [], ['code' => $exception->getCode()]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST, []);
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

        try {
            $response = $this->identConfirmator->confirm(
                $carrierId,
                $pinCode,
                $mobileNumber,
                $request->getClientIp()
            );

            return $response;
        } catch (PinVerifyProcessException $exception) {
            $message = $this->errorCodeResolver->resolveMessage($exception->getCode(), $carrierId);
            return $this->getSimpleJsonResponse($message, 200, [], ['success' => false]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }
}