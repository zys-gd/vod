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
use IdentificationBundle\WifiIdentification\Service\ErrorCodeResolver;
use IdentificationBundle\WifiIdentification\WifiIdentConfirmator;
use IdentificationBundle\WifiIdentification\WifiIdentSMSSender;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
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
     * PinIdentificationController constructor.
     * @param WifiIdentSMSSender   $identSMSSender
     * @param WifiIdentConfirmator $identConfirmator
     * @param ErrorCodeResolver    $errorCodeResolver
     */
    public function __construct(
        WifiIdentSMSSender $identSMSSender,
        WifiIdentConfirmator $identConfirmator,
        ErrorCodeResolver $errorCodeResolver
    )
    {
        $this->identSMSSender    = $identSMSSender;
        $this->identConfirmator  = $identConfirmator;
        $this->errorCodeResolver = $errorCodeResolver;
    }


    /**
     * @Route("/pincode/send",name="send_sms_pin_code")
     * @param Request $request
     * @param ISPData $ispData
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sendSMSPinCodeAction(Request $request, ISPData $ispData)
    {
        if (!$mobileNumber = $request->get('mobile_number', '')) {
            throw new BadRequestHttpException('`mobile_number` is required');
        }

        $carrierId = $ispData->getCarrierId();
        try {
            $this->identSMSSender->sendSMS($carrierId, $mobileNumber);
            return $this->getSimpleJsonResponse('Sent', 200, [], ['success' => true]);

        } catch (PinRequestProcessException $exception) {
            $message = $this->errorCodeResolver->resolveMessage($exception->getCode());
            return $this->getSimpleJsonResponse($message, 200, [], ['success' => false]);
        }
    }

    /**
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
                'redirectUrl' => $this->generateUrl('talentica_subscription.subscribe')
            ]);

        } catch (PinVerifyProcessException $exception) {
            return $this->getSimpleJsonResponse($exception->getBillingMessage(), 200, [], ['success' => false]);
        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse($exception->getMessage(), 200, [], ['success' => false]);
        }
    }
}