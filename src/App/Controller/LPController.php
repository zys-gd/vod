<?php

namespace App\Controller;

use App\Domain\ACL\Exception\AccessException;
use App\Domain\ACL\LandingPageACL;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Service\CarrierOTPVerifier;
use App\Domain\Service\Piwik\ContentStatisticSender;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\CAPTool\Exception\CapToolAccessException;
use SubscriptionBundle\Service\CAPTool\Exception\SubscriptionCapReachedOnAffiliate;
use SubscriptionBundle\Service\CAPTool\Exception\SubscriptionCapReachedOnCarrier;
use SubscriptionBundle\Service\CAPTool\Exception\VisitCapReached;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiter;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimitNotifier;
use SubscriptionBundle\Service\SubscribeUrlResolver;
use SubscriptionBundle\Service\VisitCAPTool\VisitNotifier;
use SubscriptionBundle\Service\VisitCAPTool\VisitTracker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LPController
 */
class LPController extends AbstractController implements ControllerWithISPDetection, AppControllerInterface
{

    use ResponseTrait;
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
     * @var SubscribeUrlResolver
     */
    private $subscribeUrlResolver;

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
     * @param SubscribeUrlResolver       $subscribeUrlResolver
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
        LoggerInterface $logger,
        SubscribeUrlResolver $subscribeUrlResolver
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
        $this->subscribeUrlResolver      = $subscribeUrlResolver;
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

            } catch (CapToolAccessException | AccessException $exception) {
                $this->logger->debug('CAP checking throw Access Exception');
                return RedirectResponse::create($this->defaultRedirectUrl);
            }

            $this->visitTracker->trackVisit($carrier, $campaign, $session->getId());
            $this->logger->debug('Finish CAP checking');
        }


        AffiliateVisitSaver::savePageVisitData($session, $request->query->all());

        // we can't use ISPData object as function parameter because request to LP could not contain
        // carrier data and in this case BadRequestHttpException will be throw
        $ispData            = IdentificationFlowDataExtractor::extractIspDetectionData($session);
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($session);
        $campaignToken      = AffiliateVisitSaver::extractCampaignToken($session);
        $this->contentStatisticSender->trackVisit($identificationData, $ispData ? new ISPData($ispData['carrier_id']) : null, $campaignToken);

        if ($carrier && !(bool)$this->dataStorage->readValue('is_wifi_flow') && $this->landingPageAccessResolver->isLandingDisabled($request)) {
            return new RedirectResponse($this->subscribeUrlResolver->getSubscribeRoute($carrier));
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
    public function ajaxAnnotationAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'code'     => 200,
            'response' => $this->renderView('@App/Components/Ajax/annotation.html.twig')
        ]);
    }

    /**
     * @Method("POST")
     * @Route("/after_carrier_selected", name="ajax_after_carrier_selected")
     * @return JsonResponse
     */
    public function ajaxAfterCarrierSelected(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }


        try {
            $session  = $request->getSession();
            $cid      = $session->get('campaign_id', '');
            $carrier  = $this->resolveCarrierFromRequest($request);
            $campaign = $this->resolveCampaignFromRequest($cid);

            if (!$campaign) {
                return $this->getSimpleJsonResponse('success', 200, [], [
                    'success' => true,
                ]);
            }


            try {
                $this->landingPageAccessResolver->ensureCanAccessByVisits($campaign, $carrier);
            } catch (VisitCapReached $capReached) {
                return $this->getSimpleJsonResponse('success', 200, [], [
                    'success'     => false,
                    'redirectUrl' => $this->defaultRedirectUrl
                ]);
            }

            $this->visitTracker->trackVisit($carrier, $campaign, $session->getId());


            return $this->getSimpleJsonResponse('success', 200, [], [
                'success' => true,
            ]);

        } catch (\Exception $exception) {
            return $this->getSimpleJsonResponse('success', 500, [], [
                'success' => false,
                'error'   => $exception->getMessage()
            ]);
        }
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