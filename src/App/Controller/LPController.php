<?php

namespace App\Controller;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Service\CarrierOTPVerifier;
use App\Domain\Service\ContentStatisticSender;
use App\Domain\ACL\LandingPageACL;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LPController
 */
class LPController extends AbstractController implements ControllerWithISPDetection, AppControllerInterface
{
    /**
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;
    /**
     * @var string
     */
    private $imageBaseUrl;
    /**
     * @var LandingPageACL
     */
    private $landingPageAccessResolver;
    /**
     * @var CarrierOTPVerifier
     */
    private $OTPVerifier;
    /**
     * @var string
     */
    private $defaultRedirectUrl;

    /**
     * LPController constructor.
     *
     * @param ContentStatisticSender $contentStatisticSender
     * @param CampaignRepository $campaignRepository
     * @param LandingPageACL $landingPageAccessResolver
     * @param string $imageBaseUrl
     * @param CarrierOTPVerifier $OTPVerifier
     * @param string $defaultRedirectUrl
     */
    public function __construct(
        ContentStatisticSender $contentStatisticSender,
        CampaignRepository $campaignRepository,
        LandingPageACL $landingPageAccessResolver,
        string $imageBaseUrl,
        CarrierOTPVerifier $OTPVerifier,
        string $defaultRedirectUrl
    )
    {
        $this->contentStatisticSender    = $contentStatisticSender;
        $this->campaignRepository        = $campaignRepository;
        $this->landingPageAccessResolver = $landingPageAccessResolver;
        $this->imageBaseUrl              = $imageBaseUrl;
        $this->OTPVerifier               = $OTPVerifier;
        $this->defaultRedirectUrl        = $defaultRedirectUrl;
    }


    /**
     * @\IdentificationBundle\Controller\Annotation\NoRedirectToWhoops
     * @Route("/lp",name="landing")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function landingPageAction(Request $request)
    {
        // TODO: do we need just only set flag to twig and call another macro?
        $this->OTPVerifier->forceWifi($request->getSession());

        if (!$this->landingPageAccessResolver->canAccess($request)) {
            return new RedirectResponse($this->defaultRedirectUrl);
        }

        $session = $request->getSession();
        $campaignBanner = null;
        $background = null;

        if ($cid = $request->get('cid', '')) {
            // Useless method atm.
            AffiliateVisitSaver::saveCampaignId($cid, $session);

            $campaign = $this->campaignRepository->findOneBy(['campaignToken' => $cid]);

            /** @var Campaign $campaign */
            if ($campaign) {
                $campaignBanner = $this->imageBaseUrl . '/' . $campaign->getImagePath();
                $background = $campaign->getBgColor();
            }
        };

        AffiliateVisitSaver::savePageVisitData($session, $request->query->all());

        // we can't use ISPData object as function parameter because request to LP could not contain
        // carrier data and in this case BadRequestHttpException will be throw
        $ispData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession());
        $this->contentStatisticSender->trackVisit($ispData ? new ISPData($ispData['carrier_id']) : null);

        return $this->render('@App/Common/landing.html.twig', [
            'campaignBanner' => $campaignBanner,
            'background'     => $background
        ]);
    }

    /**
     * @Route("/get_annotation", name="ajax_annotation")
     * @return JsonResponse
     */
    public function ajaxAnnotationAction()
    {
        return new JsonResponse([
            'code'     => 200,
            'response' => $this->renderView('@App/Components/Ajax/annotation.html.twig')
        ]);
    }
}