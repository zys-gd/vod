<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:45
 */

namespace App\Controller;


use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LPController extends AbstractController implements ControllerWithISPDetection
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * LPController constructor.
     */
    public function __construct(CarrierRepositoryInterface $repository)
    {
        $this->carrierRepository = $repository;
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

        return $this->render('@App/Common/landing.html.twig', [
            'isp_detection_data' => $session->get('isp_detection_data'),
            'carriers'           => $this->carrierRepository->findAllCarriers()
        ]);
    }
}