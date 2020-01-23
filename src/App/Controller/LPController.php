<?php

namespace App\Controller;

use App\Domain\ACL\Exception\AccessException;
use App\Domain\ACL\Exception\CampaignMissingParametersException;
use App\Domain\ACL\Exception\CampaignPausedException;
use App\Domain\ACL\LandingPageACL;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Service\AffiliateBannedPublisher\AffiliateBannedPublisherChecker;
use App\Domain\Service\Carrier\CarrierOTPVerifier;
use App\Domain\Service\OneClickFlow\HasCustomOneClickRedirectRules;
use App\Domain\Service\OneClickFlow\OneClickFlowCarriersProvider;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use App\Domain\Service\OneClickFlow\OneClickFlowResolver;
use App\Piwik\ContentStatisticSender;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use Doctrine\Common\Collections\ArrayCollection;
use ExtrasBundle\Controller\Traits\ResponseTrait;
use GuzzleHttp\Exception\GuzzleException;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\Exception\MissingCarrierException;
use IdentificationBundle\Identification\Service\CarrierSelector;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\CAPTool\Common\CAPToolRedirectUrlResolver;
use SubscriptionBundle\CAPTool\Subscription\Exception\CapToolAccessException;
use SubscriptionBundle\CAPTool\Subscription\Exception\VisitCapReached;
use SubscriptionBundle\CAPTool\Visit\ConstraintAvailabilityChecker;
use SubscriptionBundle\CAPTool\Visit\VisitTracker;
use SubscriptionBundle\Subscription\Subscribe\Service\SubscribeUrlResolver;
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
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var VisitTracker
     */
    private $visitTracker;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CarrierSelector
     */
    private $carrierSelector;
    /**
     * @var SubscribeUrlResolver
     */
    private $subscribeUrlResolver;
    /**
     * @var ConstraintAvailabilityChecker
     */
    private $visitConstraintChecker;
    /**
     * @var CAPToolRedirectUrlResolver
     */
    private $CAPToolRedirectUrlResolver;
    /**
     * @var OneClickFlowCarriersProvider
     */
    private $oneClickHandlerProvider;
    /**
     * @var OneClickFlowResolver
     */
    private $oneClickFlowResolver;
    /**
     * @var AffiliateBannedPublisherChecker
     */
    private $affiliateBannedPublisherChecker;

    /**
     * LPController constructor.
     *
     * @param ContentStatisticSender          $contentStatisticSender
     * @param CampaignRepository              $campaignRepository
     * @param LandingPageACL                  $landingPageAccessResolver
     * @param CarrierOTPVerifier              $OTPVerifier
     * @param string                          $defaultRedirectUrl
     * @param TemplateConfigurator            $templateConfigurator
     * @param WifiIdentificationDataStorage   $wifiIdentificationDataStorage
     * @param CarrierRepositoryInterface      $carrierRepository
     * @param VisitTracker                    $visitTracker
     * @param LoggerInterface                 $logger
     * @param CarrierSelector                 $carrierSelector
     * @param SubscribeUrlResolver            $subscribeUrlResolver
     * @param ConstraintAvailabilityChecker   $visitConstraintChecker
     * @param CAPToolRedirectUrlResolver      $CAPToolRedirectUrlResolver
     * @param OneClickFlowCarriersProvider    $oneClickFlowCarriersProvider
     * @param OneClickFlowResolver            $oneClickFlowResolver
     * @param AffiliateBannedPublisherChecker $affiliateBannedPublisherChecker
     */
    public function __construct(
        ContentStatisticSender $contentStatisticSender,
        CampaignRepository $campaignRepository,
        LandingPageACL $landingPageAccessResolver,
        CarrierOTPVerifier $OTPVerifier,
        string $defaultRedirectUrl,
        TemplateConfigurator $templateConfigurator,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        CarrierRepositoryInterface $carrierRepository,
        VisitTracker $visitTracker,
        LoggerInterface $logger,
        CarrierSelector $carrierSelector,
        SubscribeUrlResolver $subscribeUrlResolver,
        ConstraintAvailabilityChecker $visitConstraintChecker,
        CAPToolRedirectUrlResolver $CAPToolRedirectUrlResolver,
        OneClickFlowCarriersProvider $oneClickFlowCarriersProvider,
        OneClickFlowResolver $oneClickFlowResolver,
        AffiliateBannedPublisherChecker $affiliateBannedPublisherChecker
    )
    {
        $this->contentStatisticSender          = $contentStatisticSender;
        $this->campaignRepository              = $campaignRepository;
        $this->landingPageAccessResolver       = $landingPageAccessResolver;
        $this->OTPVerifier                     = $OTPVerifier;
        $this->defaultRedirectUrl              = $defaultRedirectUrl;
        $this->templateConfigurator            = $templateConfigurator;
        $this->wifiIdentificationDataStorage   = $wifiIdentificationDataStorage;
        $this->carrierRepository               = $carrierRepository;
        $this->visitTracker                    = $visitTracker;
        $this->logger                          = $logger;
        $this->carrierSelector                 = $carrierSelector;
        $this->subscribeUrlResolver            = $subscribeUrlResolver;
        $this->visitConstraintChecker          = $visitConstraintChecker;
        $this->CAPToolRedirectUrlResolver      = $CAPToolRedirectUrlResolver;
        $this->oneClickHandlerProvider         = $oneClickFlowCarriersProvider;
        $this->oneClickFlowResolver            = $oneClickFlowResolver;
        $this->affiliateBannedPublisherChecker = $affiliateBannedPublisherChecker;
    }


    /**
     * @\IdentificationBundle\Controller\Annotation\NoRedirectToWhoops
     * @Route("/lp",name="landing")
     *
     * @param Request $request
     *
     * @return Response
     * @throws GuzzleException
     * @throws TemplateNotFoundException
     */
    public function landingPageAction(Request $request)
    {
        $session = $request->getSession();

        $cid = $request->get('cid', '');

        /** @var Campaign $campaign */
        $campaign = $this->resolveCampaignFromRequest($cid);
        if ($cid && !$campaign) {
            return RedirectResponse::create($this->defaultRedirectUrl);
        }

        $carrier = $this->resolveCarrierFromRequest($request);

        /** @var Campaign $campaign */
        if ($campaign) {
            if ($campaign->getIsPause()) { // TODO: technical debt
                return RedirectResponse::create($this->defaultRedirectUrl);
            }

            try {
                $this->landingPageAccessResolver->ensureCampaignHaveAllParametersPassed($request, $campaign);
            } catch (CampaignMissingParametersException $exception) {
                $this->logger->debug('Parameters missing', ['params' => $request->query->all()]);
                return RedirectResponse::create($this->defaultRedirectUrl);
            }

            // Useless method atm.
            AffiliateVisitSaver::saveCampaignId($cid, $session);

            if ($this->affiliateBannedPublisherChecker->isPublisherBanned($request->query->all(), $campaign->getAffiliate(), $carrier)) {
                return new RedirectResponse($this->defaultRedirectUrl);
            }
        }

        if ($carrier && $campaign) {
            $this->logger->debug('Start CAP checking', ['carrier' => $carrier, 'campaign' => $campaign]);

            try {
                $this->landingPageAccessResolver->ensureCanAccess($campaign, $carrier);
            } catch (CapToolAccessException $exception) {
                $this->logger->debug(sprintf('CAP checking throw %s', get_class($exception)));
                $url = $this->CAPToolRedirectUrlResolver->resolveUrl($exception);
                return RedirectResponse::create($url);
            } catch (AccessException $exception) {
                $this->logger->debug('CAP checking throw Access Exception');
                return RedirectResponse::create($this->defaultRedirectUrl);
            }

            if ($this->visitConstraintChecker->isCapEnabledForAffiliate($campaign->getAffiliate())) {
                $this->visitTracker->trackVisit($carrier, $campaign, $session->getId());
            }
            $this->logger->debug('Finish CAP checking');
        }

        if ($cid) {
            AffiliateVisitSaver::savePageVisitData($session, $request->query->all());
        }

        $billingCarrierId    = IdentificationFlowDataExtractor::extractBillingCarrierId($session);
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($request->getSession());
        $isWifiFlow          = $billingCarrierId ? false : true;
        $this->contentStatisticSender->trackVisit($session);

        if ($carrier) {
            $clickHandler = $this->oneClickHandlerProvider->get($carrier->getBillingCarrierId(), OneClickFlowParameters::LP_OFF);
            if ($clickHandler && $this->oneClickFlowResolver->isLandingDisabled($carrier, $campaign)) {
                if ($clickHandler instanceof HasCustomOneClickRedirectRules) {
                    return new RedirectResponse($clickHandler->getRedirectUrl());
                }
                $redirectUrl = $this->subscribeUrlResolver->getSubscribeRoute($request, $carrier, $identificationToken);
                return new RedirectResponse($redirectUrl);
            }
        }

        if (!$cid) {
            $this->OTPVerifier->forceWifi($session);
        }

        $templateName = $isWifiFlow ? 'landing_wifi' : 'landing_3g';
        $template     = $this->templateConfigurator->getTemplate($templateName, (int)$billingCarrierId);

        return $this->render($template);
    }

    /**
     * @Route(
     *     "/lp/select-carrier-wifi",
     *     name="select_carrier_wifi",
     *     methods={"GET"},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws TemplateNotFoundException
     */
    public function selectCarrierAction(Request $request)
    {
        if (!$carrierId = $request->get('carrierId', '')) {
            $this->carrierSelector->removeCarrier();

            return new JsonResponse(['error' => 'Missing `carrier_id` parameter'], Response::HTTP_BAD_REQUEST);
        }

        if (!(bool)$this->wifiIdentificationDataStorage->isWifiFlow()) {
            return new JsonResponse(['error' => 'Error flow'], Response::HTTP_BAD_REQUEST);
        }

        $this->carrierSelector->selectCarrier((int)$carrierId);

        $session  = $request->getSession();
        $cid      = $session->get('campaign_id', '');
        $campaign = $this->resolveCampaignFromRequest($cid);

        if ($campaign) {
            $carrier = $this->resolveCarrierFromRequest($request);
            try {
                $this->landingPageAccessResolver->ensureCanAccessByVisits($campaign, $carrier);
            } catch (VisitCapReached $capReached) {
                return $this->getSimpleJsonResponse('success', Response::HTTP_BAD_REQUEST, [], [
                    'redirectUrl' => $this->CAPToolRedirectUrlResolver->resolveUrl($capReached)
                ]);
            }

            $this->visitTracker->trackVisit($carrier, $campaign, $session->getId());
        }

        try {
            $template = $this->templateConfigurator->getTemplate('landing_wifi', (int)$carrierId); // @App/Common/landing_wifi.html.twig
            $html     = $this->renderView($template);

            return new JsonResponse($html, Response::HTTP_OK);
        } catch (MissingCarrierException $exception) {
            return new JsonResponse([
                'error' => $exception->getMessage()
            ], $exception->getCode());
        }
    }

    /**
     * @Route("/lp/fetch-carriers-for-country", name="fetch_carriers_for_country", methods={"GET"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function fetchCarrierForCountry(Request $request)
    {
        if (!$countryCode = $request->get('countryCode')) {
            return new JsonResponse(['error' => 'Missing `countryCode` parameter']);
        }

        $countryCarriers = new ArrayCollection(
            $this->carrierRepository->findBy(['published' => true, 'countryCode' => $countryCode])
        );

        $resultMapper = $countryCarriers->map(function (Carrier $carrier) {
            return [
                'id'   => $carrier->getBillingCarrierId(),
                'name' => $carrier->getName()
            ];
        })->toArray();


        return new JsonResponse($resultMapper, Response::HTTP_OK);
    }

    /**
     * @Route("/lp/resest-wifi-lp", name="reset_wifi_lp", methods={"GET"}, condition="request.isXmlHttpRequest()")
     * @return string
     * @throws TemplateNotFoundException
     */
    public function resetWifiLP()
    {
        $this->carrierSelector->removeCarrier();

        $template = $this->templateConfigurator->getTemplate('landing_wifi', 0);
        $html     = $this->renderView($template);

        return new JsonResponse($html, Response::HTTP_OK);
    }

    /**
     * @Route("/lp/pin-confirm", name="pin_confirm", methods={"GET"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws TemplateNotFoundException
     */
    public function pinConfirmWifiLP(Request $request)
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession());

        $template = $this->templateConfigurator->getTemplate('landing_wifi', $billingCarrierId);

        $html = $this->renderView($template, ['phoneNumber' => (string)$request->get('phone', '')]);

        return new JsonResponse($html, Response::HTTP_OK);
    }

    /**
     * @Route("/lp/change-number", name="change_number", methods={"GET"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws TemplateNotFoundException
     */
    public function changeNumberWifiLP(Request $request)
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession());

        $template = $this->templateConfigurator->getTemplate('landing_wifi', $billingCarrierId);

        $html = $this->renderView($template, ['phoneNumber' => (string)$request->get('phone', '')]);

        return new JsonResponse($html, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return Carrier|null
     */
    private function resolveCarrierFromRequest(Request $request): ?CarrierInterface
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession());

        if (!empty($billingCarrierId)) {
            return $this->carrierRepository->findOneByBillingId($billingCarrierId);
        }

        return null;
    }

    /**
     * @param $cid
     *
     * @return Campaign|null
     */
    private function resolveCampaignFromRequest($cid): ?Campaign
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->findOneByCampaignToken($cid);

        return $campaign ?? null;
    }
}