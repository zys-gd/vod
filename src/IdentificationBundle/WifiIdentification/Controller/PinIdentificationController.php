<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:26
 */

namespace IdentificationBundle\WifiIdentification\Controller;


use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\Controller\Annotation\DenyUsersWithoutISPData;
use IdentificationBundle\WifiIdentification\WifiIdentConfirmator;
use IdentificationBundle\WifiIdentification\WifiIdentSMSSender;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PinIdentificationController extends AbstractController

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
     * PinIdentificationController constructor.
     */
    public function __construct(WifiIdentSMSSender $identSMSSender, WifiIdentConfirmator $identConfirmator)
    {
        $this->identSMSSender   = $identSMSSender;
        $this->identConfirmator = $identConfirmator;
    }


    /**
     * @InjectISPDetectionData("ispData")
     * @Route("/pincode/send",name="send_sms_pin_code")
     * @param Request $request
     * @param array   $ispData
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sendSMSPinCodeAction(Request $request, array $ispData)
    {
        if (!$mobileNumber = $request->get('mobile_number', '')) {
            throw new BadRequestHttpException('`mobile_number` is required');
        }

        $carrierId = $ispData['carrier_id'];

        try {
            $this->identSMSSender->sendSMS((int)$carrierId, $mobileNumber);
            return $this->getSimpleJsonResponse('Sent', 200, [], ['success' => true]);
        } catch (PinRequestProcessException $exception) {
            return $this->getSimpleJsonResponse($exception->getBillingMessage(), 200, [], ['success' => false]);
        }
    }

    /**
     * @InjectISPDetectionData("ispData")
     * @Route("/pincode/confirm",name="confirm_sms_pin_code")
     * @param Request $request
     * @param array   $ispData
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function confirmSMSPinCodeAction(Request $request, array $ispData)
    {
        if (!$pinCode = $request->get('pin_code', '')) {
            throw new BadRequestHttpException('`pin_code` is required');
        }

        $carrierId = $ispData['carrier_id'];

        $this->identConfirmator->confirm((int)$carrierId, $pinCode);


        return $this->getSimpleJsonResponse('Sent');
    }
}