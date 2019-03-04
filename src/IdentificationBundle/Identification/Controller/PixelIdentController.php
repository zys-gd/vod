<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 12:51
 */

namespace IdentificationBundle\Identification\Controller;


use ExtrasBundle\SignatureCheck\Annotation\SignatureCheckIsRequired;
use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Identification\Common\Pixel\PixelIdentConfirmer;
use IdentificationBundle\Identification\Common\Pixel\PixelIdentVerifier;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\RouteProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PixelIdentController extends AbstractController
{
    /**
     * @var PixelIdentVerifier
     */
    private $pixelIdentVerifier;

    /**
     * @var PixelIdentConfirmer
     */
    private $confirmer;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var DataProvider
     */
    private $billingDataProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    public function __construct(
        PixelIdentVerifier $pixelIdentVerifier,
        PixelIdentConfirmer $confirmer,
        RouteProvider $routeProvider,
        DataProvider $billingDataProvider,
        IdentificationDataStorage $identificationDataStorage
    )
    {

        $this->pixelIdentVerifier        = $pixelIdentVerifier;
        $this->confirmer                 = $confirmer;
        $this->routeProvider             = $routeProvider;
        $this->billingDataProvider       = $billingDataProvider;
        $this->identificationDataStorage = $identificationDataStorage;
    }


    /**
     * @SignatureCheckIsRequired
     * @Route("/pixel/show-page",name="show_pixel_page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPixelAction(Request $request)
    {
        $pixelUrl  = $request->get('pixelUrl', '');
        $carrier   = $request->get('carrier', '');
        $processId = $request->get('processId', '');


        if (!$pixelUrl) {
            throw new BadRequestHttpException('Missing `pixelUrl` parameter');
        }

        try {
            $result = $this->billingDataProvider->getProcessData($processId);

            if (!$result->isPixel()) {
                throw new BadRequestHttpException("Invalid ident type.");
            }

        } catch (BillingFrameworkProcessException $exception) {
            throw new BadRequestHttpException("Pixel ident is not started yet");
        }

        if ($this->identificationDataStorage->readValue('subscribeAfterIdent')) {
            $this->identificationDataStorage->cleanValue('subscribeAfterIdent');
            $successUrl = $this->generateUrl('subscription.subscribe');
        } else {
            $successUrl = $this->routeProvider->getLinkToHomepage();
        }


        return $this->render('@Identification/pixelIdent/show_pixel.twig', [
            'pixelUrl'        => $pixelUrl,
            'confirmUrl'      => $this->generateUrl('confirm_pixel_ident', ['processId' => $processId]),
            'successUrl'      => $successUrl,
            'failureUrl'      => $this->routeProvider->getLinkToHomepage(['err' => 'pixel_ident_timeout']),
            'statusActionUrl' => $this->generateUrl('pixel_ident_status', [
                'carrier'   => $carrier,
                'processId' => $processId
            ])
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/pixel/status",name="pixel_ident_status")
     * @param Request $request
     * @return JsonResponse
     */
    public function pixelStatusAction(Request $request)
    {
        $carrier   = $request->get('carrier', '');
        $processId = $request->get('processId', '');

        if (!$processId) {
            throw new BadRequestHttpException('Missing `processId` parameter');
        }
        if (!$carrier) {
            throw new BadRequestHttpException('Missing `carrier` parameter');
        }

        $result = $this->pixelIdentVerifier->isIdentSuccess((int)$carrier, $processId);
        if ($result) {
            return new JsonResponse(['result' => true]);
        } else {
            return new JsonResponse(['result' => false]);
        }
    }

    /**
     * @Method("POST")
     * @Route("/pixel/confirm",name="confirm_pixel_ident")
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmPixelIdentAction(Request $request, ISPData $ISPData, DeviceData $deviceData)
    {
        $processId = $request->get('processId', '');

        try {
            $this->confirmer->confirmIdent($processId, $ISPData->getCarrierId(), $deviceData);
            return new JsonResponse(['result' => true]);
        } catch (\Exception $exception) {
            return new JsonResponse(['result' => false]);
        }


    }
}