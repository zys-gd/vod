<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:26
 */

namespace IdentificationBundle\WifiIdentification\Controller;


use ExtrasBundle\API\Controller\APIControllerInterface;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\BillingFramework\Process\Exception\PinVerifyProcessException;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Exception\MissingCarrierException;
use IdentificationBundle\Identification\Service\CarrierSelector;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\Service\ErrorCodeResolver;
use IdentificationBundle\WifiIdentification\WifiIdentConfirmator;
use IdentificationBundle\WifiIdentification\WifiIdentSMSSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\CAPTool\LimiterNotifier;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     * @var CarrierSelector
     */
    private $carrierSelector;
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
     * @var LimiterNotifier
     */
    private $limiterNotifier;

    /**
     * PinIdentificationController constructor.
     * @param WifiIdentSMSSender         $identSMSSender
     * @param WifiIdentConfirmator       $identConfirmator
     * @param ErrorCodeResolver          $errorCodeResolver
     * @param CarrierSelector            $carrierSelector
     * @param SubscriptionLimiter        $limiter
     * @param string                     $defaultRedirectUrl
     * @param CarrierRepositoryInterface $carrierRepository
     * @param LimiterNotifier            $limiterNotifier
     */
    public function __construct(
        WifiIdentSMSSender $identSMSSender,
        WifiIdentConfirmator $identConfirmator,
        ErrorCodeResolver $errorCodeResolver,
        CarrierSelector $carrierSelector,
        SubscriptionLimiter $limiter,
        string $defaultRedirectUrl,
        CarrierRepositoryInterface $carrierRepository,
        LimiterNotifier $limiterNotifier

    )
    {
        $this->identSMSSender     = $identSMSSender;
        $this->identConfirmator   = $identConfirmator;
        $this->errorCodeResolver  = $errorCodeResolver;
        $this->carrierSelector    = $carrierSelector;
        $this->limiter            = $limiter;
        $this->defaultRedirectUrl = $defaultRedirectUrl;
        $this->carrierRepository  = $carrierRepository;
        $this->limiterNotifier    = $limiterNotifier;
    }


    /**
     * @Method("POST")
     * @Route("/pincode/select-carrier",name="select_carrier_for_pin_code_form")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function selectCarrierAction(Request $request)
    {
        if (!$carrierId = $request->get('carrier_id', '')) {
            throw new BadRequestHttpException('`carrier_id` is required');
        }

        try {
            $this->carrierSelector->selectCarrier((int)$carrierId);
            return $this->getSimpleJsonResponse('Successfully selected', 200, [], ['success' => true]);
        } catch (MissingCarrierException $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }

    }

    /**
     * @Method("POST")
     * @Route("/pincode/send",name="send_sms_pin_code")
     * @param Request $request
     * @param ISPData $ispData
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendSMSPinCodeAction(Request $request, ISPData $ispData)
    {
        if (!$mobileNumber = $request->get('mobile_number', '')) {
            throw new BadRequestHttpException('`mobile_number` is required');
        }


        if ($this->limiter->isSubscriptionLimitReached($request->getSession())) {

            $carrier = $this->carrierRepository->findOneByBillingId($ispData->getCarrierId());
            $this->limiterNotifier->notifyLimitReachedForCarrier($carrier);

            return $this->getSimpleJsonResponse('Subscription limit has been reached', 200, [], [
                'success' => false, 'redirectUrl' => $this->defaultRedirectUrl
            ]);
        }

        $this->limiter->reserveSlotForSubscription($request->getSession());

        $carrierId = $ispData->getCarrierId();
        try {
            $this->identSMSSender->sendSMS($carrierId, $mobileNumber);
            return $this->getSimpleJsonResponse('Sent', 200, [], ['success' => true]);

        } catch (PinRequestProcessException $exception) {
            $message = $this->errorCodeResolver->resolveMessage($exception->getCode());
            return $this->getSimpleJsonResponse($message, 200, [], ['success' => false]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }

    /**
     * @Method("POST")
     * @Route("/pincode/confirm",name="confirm_sms_pin_code")
     * @param Request $request
     * @param ISPData $ispData
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
            $this->identConfirmator->confirm(
                $carrierId,
                $pinCode,
                $mobileNumber,
                $request->getClientIp()
            );
            return $this->getSimpleJsonResponse('Confirmed', 200, [], [
                'success'     => true,
                'redirectUrl' => $this->generateUrl('subscription.subscribe')
            ]);

        } catch (PinVerifyProcessException $exception) {
            return $this->getSimpleJsonResponse($exception->getBillingMessage(), 200, [], ['success' => false]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }

    /**
     * @Method("POST")
     * @Route("/pincode/select-carrier-with-send-pin",name="select_carrier_with_send_pin_code")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function selectCarrierWithSendSMSPinCodeAction(Request $request)
    {
        if (!$carrierId = $request->get('carrier_id', '')) {
            throw new BadRequestHttpException('`carrier_id` is required');
        }

        try {
            $this->carrierSelector->selectCarrier((int)$carrierId);
        } catch (MissingCarrierException $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }

        if (!$mobileNumber = $request->get('mobile_number', '')) {
            throw new BadRequestHttpException('`mobile_number` is required');
        }

        if ($this->limiter->isSubscriptionLimitReached($request->getSession())) {

            $carrier = $this->carrierRepository->findOneByBillingId((int)$carrierId);
            $this->limiterNotifier->notifyLimitReachedForCarrier($carrier);

            return $this->getSimpleJsonResponse('Subscription limit has been reached', 200, [], [
                'success' => false, 'redirectUrl' => $this->defaultRedirectUrl
            ]);
        }
        $this->limiter->reserveSlotForSubscription($request->getSession());

        try {
            $this->identSMSSender->sendSMS($carrierId, $mobileNumber);
            return $this->getSimpleJsonResponse('Sent', 200, [], ['success' => true]);

        } catch (PinRequestProcessException $exception) {
            $message = $this->errorCodeResolver->resolveMessage($exception->getCode());
            return $this->getSimpleJsonResponse($message, 200, [], ['success' => false]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }
}