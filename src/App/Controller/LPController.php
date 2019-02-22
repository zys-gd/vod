<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:45
 */

namespace App\Controller;


use App\Domain\Service\ContentStatisticSender;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LPController extends AbstractController implements ControllerWithISPDetection, AppControllerInterface
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var SubscriptionPackRepository
     */
    private $subscriptionPackRepository;
    /**
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;

    /**
     * LPController constructor
     *
     * @param SubscriptionPackRepository $subscriptionPackRepository
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ContentStatisticSender $contentStatisticSender
     */
    public function __construct(
        SubscriptionPackRepository $subscriptionPackRepository,
        CarrierRepositoryInterface $carrierRepository,
        ContentStatisticSender $contentStatisticSender
    ) {
        $this->subscriptionPackRepository = $subscriptionPackRepository;
        $this->carrierRepository          = $carrierRepository;
        $this->contentStatisticSender     = $contentStatisticSender;
    }


    /**
     * @\IdentificationBundle\Controller\Annotation\NoRedirectToWhoops
     * @Route("/lp",name="landing")
     * @param Request $request
     * @return Response
     */
    public function landingPageAction(Request $request)
    {
        $session = $request->getSession();

        if ($cid = $request->get('cid', '')) {
            // Useless method atm.
            AffiliateVisitSaver::saveCampaignId($cid, $session);
        };

        AffiliateVisitSaver::savePageVisitData($session, $request->query->all());

        $carrierInterfaces = $this->carrierRepository->findAllCarriers();

        /** @var SubscriptionPack[] $subpacks */
        $subpacks = $this->subscriptionPackRepository->findAll();


        $subpackCarriers = [];
        foreach ($subpacks as $subpack) {
            $subpackCarriers[] = $subpack->getCarrierId();
        }

        $carrierInterfaces = array_filter($carrierInterfaces, function (CarrierInterface $carrier) use ($subpackCarriers) {
            return in_array($carrier->getBillingCarrierId(), $subpackCarriers);
        });

        $this->contentStatisticSender->trackVisit();

        return $this->render('@App/Common/landing.html.twig', [
            'isp_detection_data' => $session->get('isp_detection_data'),
            'carriers'           => $carrierInterfaces
        ]);
    }
}