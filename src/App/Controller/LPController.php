<?php

namespace App\Controller;

use App\Domain\ACL\Exception\AccessException;
use App\Domain\ACL\Exception\SubscriptionCapReachedOnAffiliate;
use App\Domain\ACL\Exception\SubscriptionCapReachedOnCarrier;
use App\Domain\ACL\Exception\VisitCapReached;
use App\Domain\ACL\LandingPageACL;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Service\CarrierOTPVerifier;
use App\Domain\Service\ContentStatisticSender;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimitNotifier;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiter;
use SubscriptionBundle\Service\VisitCAPTool\VisitNotifier;
use SubscriptionBundle\Service\VisitCAPTool\VisitTracker;
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
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var SubscriptionLimiter
     */
    private $limiter;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var SubscriptionLimitNotifier
     */
    private $subscriptionLimitNotifier;
    /**
     * @var VisitTracker
     */
    private $visitTracker;
    /**
     * @var VisitNotifier
     */
    private $visitNotifier;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LPController constructor.
     *
     * @param ContentStatisticSender     $contentStatisticSender
     * @param CampaignRepository         $campaignRepository
     * @param LandingPageACL             $landingPageAccessResolver
     * @param string                     $imageBaseUrl
     * @param CarrierOTPVerifier         $OTPVerifier
     * @param string                     $defaultRedirectUrl
     * @param IdentificationDataStorage  $dataStorage
     * @param SubscriptionLimiter        $limiter
     * @param SubscriptionLimitNotifier  $subscriptionLimitNotifier
     * @param CarrierRepositoryInterface $carrierRepository
     * @param VisitTracker               $visitTracker
     * @param VisitNotifier              $notifier
     * @param LoggerInterface            $logger
     */
    public function __construct(
        ContentStatisticSender $contentStatisticSender,
        CampaignRepository $campaignRepository,
        LandingPageACL $landingPageAccessResolver,
        string $imageBaseUrl,
        CarrierOTPVerifier $OTPVerifier,
        string $defaultRedirectUrl,
        IdentificationDataStorage $dataStorage,
        SubscriptionLimiter $limiter,
        SubscriptionLimitNotifier $subscriptionLimitNotifier,
        CarrierRepositoryInterface $carrierRepository,
        VisitTracker $visitTracker,
        VisitNotifier $notifier,
        LoggerInterface $logger
    )
    {
        $this->contentStatisticSender    = $contentStatisticSender;
        $this->campaignRepository        = $campaignRepository;
        $this->landingPageAccessResolver = $landingPageAccessResolver;
        $this->imageBaseUrl              = $imageBaseUrl;
        $this->OTPVerifier               = $OTPVerifier;
        $this->defaultRedirectUrl        = $defaultRedirectUrl;
        $this->dataStorage               = $dataStorage;
        $this->limiter                   = $limiter;
        $this->carrierRepository         = $carrierRepository;
        $this->subscriptionLimitNotifier = $subscriptionLimitNotifier;
        $this->visitTracker              = $visitTracker;
        $this->visitNotifier             = $notifier;
        $this->logger                    = $logger;
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
        $session        = $request->getSession();
        $campaignBanner = null;
        $background     = null;

        $cid      = $request->get('cid', '');
        $campaign = $this->resolveCampaignFromRequest($cid);
        if ($cid && !$campaign) {
            return RedirectResponse::create($this->defaultRedirectUrl);
        }

        /** @var Campaign $campaign */
        if ($campaign) {
            // Useless method atm.
            AffiliateVisitSaver::saveCampaignId($cid, $session);
            $campaignBanner = $this->imageBaseUrl . '/' . $campaign->getImagePath();
            $background     = $campaign->getBgColor();
        }

        $carrier = $this->resolveCarrierFromRequest($request);
        if ($carrier && $campaign) {
            $this->logger->debug('Start CAP checking');
            try {
                $this->landingPageAccessResolver->ensureCanAccess($campaign, $carrier);

            } catch (SubscriptionCapReachedOnCarrier $e) {
                $this->logger->debug('CAP checking throw SubscriptionCapReachedOnCarrier');
                $this->subscriptionLimitNotifier->notifyLimitReachedForCarrier($e->getCarrier());
                return RedirectResponse::create($this->defaultRedirectUrl);

            } catch (SubscriptionCapReachedOnAffiliate $e) {
                $this->logger->debug('CAP checking throw SubscriptionCapReachedOnAffiliate');
                $this->subscriptionLimitNotifier->notifyLimitReachedByAffiliate($e->getConstraint(), $e->getCarrier());
                return RedirectResponse::create($this->defaultRedirectUrl);

            } catch (VisitCapReached $exception) {
                $this->logger->debug('CAP checking throw VisitCapReached');
                $this->visitNotifier->notifyLimitReached($exception->getConstraint(), $carrier);
                return RedirectResponse::create($this->defaultRedirectUrl);

            } catch (AccessException $exception) {
                $this->logger->debug('CAP checking throw AccessException');
                return RedirectResponse::create($this->defaultRedirectUrl);

            }

            $this->visitTracker->trackVisit($carrier, $campaign, $session->getId());
            $this->logger->debug('Finish CAP checking');
        }


        AffiliateVisitSaver::savePageVisitData($session, $request->query->all());

        // we can't use ISPData object as function parameter because request to LP could not contain
        // carrier data and in this case BadRequestHttpException will be throw
        $ispData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession());
        $this->contentStatisticSender->trackVisit($request->getSession(), $ispData ? new ISPData($ispData['carrier_id']) : null);

        if (!(bool)$this->dataStorage->readValue('is_wifi_flow') && $this->landingPageAccessResolver->isLandingDisabled($request)) {
            return new RedirectResponse($this->generateUrl('identify_and_subscribe'));
        }

        if (!$cid) {
            $this->OTPVerifier->forceWifi($session);
        }

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

    /**
     * @param Request $request
     *
     * @return Carrier|null
     */
    private function resolveCarrierFromRequest(Request $request): ?Carrier
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession());
        $billingCarrierId = (int)$ispDetectionData['carrier_id'] ?? null;
        if (!empty($billingCarrierId)) {
            return $this->carrierRepository->findOneByBillingId($billingCarrierId);
        }
        else {
            return null;
        }
    }

    private function resolveCampaignFromRequest($cid): ?Campaign
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->findOneBy([
            'campaignToken' => $cid,
            'isPause'       => false
        ]);

        if ($campaign) {
            return $campaign;
        }
        else {
            return null;
        }

    }
}