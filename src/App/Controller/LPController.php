<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:45
 */

namespace App\Controller;


use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Service\ContentStatisticSender;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Service\ConstraintByAffiliateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @var ConstraintByAffiliateService
     */
    private $constraintByAffiliateService;

    /**
     * LPController constructor.
     *
     * @param ContentStatisticSender       $contentStatisticSender
     * @param CampaignRepository           $campaignRepository
     * @param ConstraintByAffiliateService $constraintByAffiliateService
     * @param string                       $imageBaseUrl
     */
    public function __construct(
        ContentStatisticSender $contentStatisticSender,
        CampaignRepository $campaignRepository,
        ConstraintByAffiliateService $constraintByAffiliateService,
        string $imageBaseUrl
    )
    {
        $this->contentStatisticSender       = $contentStatisticSender;
        $this->campaignRepository           = $campaignRepository;
        $this->constraintByAffiliateService = $constraintByAffiliateService;
        $this->imageBaseUrl                 = $imageBaseUrl;
    }


    /**
     * @\IdentificationBundle\Controller\Annotation\NoRedirectToWhoops
     * @Route("/lp",name="landing")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function landingPageAction(Request $request)
    {
        $session        = $request->getSession();
        $campaignBanner = null;
        $background     = null;

        if ($cid = $request->get('cid', '')) {
            // Useless method atm.
            AffiliateVisitSaver::saveCampaignId($cid, $session);

            $campaign = $this->campaignRepository->findOneBy(['campaignToken' => $cid]);

            /** @var Campaign $campaign */
            if ($campaign) {
                $constraintsCheckResult = $this->constraintByAffiliateService->handleLandingPageRequest($campaign);

                if ($constraintsCheckResult) {
                    return $constraintsCheckResult;
                }

                $campaignBanner = $this->imageBaseUrl . '/' . $campaign->getImagePath();
                $background     = $campaign->getBgColor();

                $this->constraintByAffiliateService->updateVisitCounter($campaign->getAffiliate());
            }
        };

        AffiliateVisitSaver::savePageVisitData($session, $request->query->all());
        $this->contentStatisticSender->trackVisit();

        return $this->render('@App/Common/landing.html.twig', [
            'campaignBanner' => $campaignBanner,
            'background'     => $background
        ]);
    }

    /**
     * @Route("/get_annotation", name="ajax_annotation")
     *
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