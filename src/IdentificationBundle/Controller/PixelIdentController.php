<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 12:51
 */

namespace IdentificationBundle\Controller;


use IdentificationBundle\Identification\Common\Pixel\PixelIdentConfirmer;
use IdentificationBundle\Identification\Common\Pixel\PixelIdentVerifier;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function __construct(PixelIdentVerifier $pixelIdentVerifier, PixelIdentConfirmer $confirmer)
    {
        $this->pixelIdentVerifier = $pixelIdentVerifier;
        $this->confirmer          = $confirmer;
    }


    /**
     * @Route("/pixel")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPixelAction(Request $request)
    {
        $pixelUrl   = $request->get('pixelUrl', '');
        $carrier    = $request->get('carrier', '');
        $processId  = $request->get('processId', '');
        $successUrl = $request->get('successUrl', '');

        if (!$pixelUrl) {
            throw new BadRequestHttpException('Missing `pixelUrl` parameter');
        }

        return $this->render('@Identification/pixelIdent/showPixel.twig', [
            'pixelUrl'        => $pixelUrl,
            'successUrl'      => $successUrl,
            'failureUrl'      => $this->generateUrl('index', ['err' => 'pixel_ident_timeout']),
            'statusActionUrl' => $this->generateUrl('identification_pixelident_pixelstatus', [
                'carrier'   => $carrier,
                'processId' => $processId
            ])
        ]);
    }

    /**
     * @Route("/pixel-status")
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
     * @Route("/confirm-pixel")
     * @param Request $request
     * @return RedirectResponse
     */
    public function confirmPixelIdentAction(Request $request)
    {
        $backUrl            = $request->get('backUrl', '');
        $processId          = $request->get('processId', '');
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($request->getSession());

        if (!$identificationData) {
            throw new BadRequestHttpException('You are not identified yet');
        }

        try {
            $this->confirmer->confirmIdent(
                $processId,
                $identificationData
            );
            return new RedirectResponse($backUrl);
        } catch (BillingFrameworkProcessException $exception) {

        }


    }
}