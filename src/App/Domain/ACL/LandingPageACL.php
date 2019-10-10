<?php

namespace App\Domain\ACL;

use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Exception\CampaignAccessException;
use App\Domain\ACL\Exception\CampaignPausedException;
use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CampaignScheduleRepository;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Service\AffiliateBannedPublisher\AffiliateBannedPublisherChecker;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use App\Domain\Service\OneClickFlow\OneClickFlowChecker;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\CAPTool\Subscription\Exception\SubscriptionCapReachedOnAffiliate;
use SubscriptionBundle\CAPTool\Subscription\Exception\SubscriptionCapReachedOnCarrier;
use SubscriptionBundle\CAPTool\Subscription\Exception\VisitCapReached;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\CAPTool\Subscription\Limiter\SubscriptionCapChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class LandingPageAccessResolver
 */
class LandingPageACL
{
    /**
     * @var VisitConstraintByAffiliate
     */
    private $visitConstraintByAffiliate;

    /**
     * @var VisitAccessorByCampaign
     */
    private $visitAccessorByCampaign;

    /**
     * @var SubscriptionCapChecker
     */
    private $carrierCapChecker;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CampaignScheduleRepository
     */
    private $campaignScheduleRepository;
    /**
     * @var AffiliateBannedPublisherChecker
     */
    private $affiliateBannedPublisherChecker;
    /**
     * @var OneClickFlowChecker
     */
    private $oneClickFlowChecker;

    /**
     * LandingPageAccessResolver constructor
     *
     * @param VisitConstraintByAffiliate      $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign         $visitAccessorByCampaign
     * @param CarrierRepository               $carrierRepository
     * @param CampaignRepository              $campaignRepository
     * @param SessionInterface                $session
     * @param SubscriptionCapChecker          $subscriptionCapChecker
     * @param LoggerInterface                 $logger
     * @param CampaignScheduleRepository      $campaignScheduleRepository
     * @param AffiliateBannedPublisherChecker $affiliateBannedPublisherChecker
     * @param OneClickFlowChecker             $oneClickFlowChecker
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        SessionInterface $session,
        SubscriptionCapChecker $subscriptionCapChecker,
        LoggerInterface $logger,
        CampaignScheduleRepository $campaignScheduleRepository,
        AffiliateBannedPublisherChecker $affiliateBannedPublisherChecker,
        OneClickFlowChecker $oneClickFlowChecker
    )
    {
        $this->visitConstraintByAffiliate      = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign         = $visitAccessorByCampaign;
        $this->carrierCapChecker               = $subscriptionCapChecker;
        $this->logger                          = $logger;
        $this->carrierRepository               = $carrierRepository;
        $this->campaignRepository              = $campaignRepository;
        $this->session                         = $session;
        $this->campaignScheduleRepository      = $campaignScheduleRepository;
        $this->affiliateBannedPublisherChecker = $affiliateBannedPublisherChecker;
        $this->oneClickFlowChecker             = $oneClickFlowChecker;
    }

    /**
     * @param Campaign $campaign
     * @param Carrier  $carrier
     *
     * @return void
     */
    public function ensureCanAccess(CampaignInterface $campaign, Carrier $carrier): void
    {
        if ($campaign->getIsPause()) {
            $this->logger->debug('CAP checking on LP', ['message' => 'Campaign on pause']);
            throw new CampaignPausedException();
        }

        if (!$this->visitAccessorByCampaign->canVisit($campaign, $carrier)) {
            $this->logger->debug('CAP checking on LP', ['message' => 'visitAccessorByCampaign say: cant visit']);
            throw new CampaignAccessException($campaign);
        }

        $this->ensureSubscribeCapIsNotReachedByCarrier($carrier);

        $affiliate = $campaign->getAffiliate();

        foreach ($affiliate->getConstraints() as $constraint) {
            /** @var ConstraintByAffiliate $constraint */
            if ($carrier && $carrier->getUuid() !== $constraint->getCarrier()->getUuid()) {
                continue;
            }

            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE) {
                $this->ensureSubscribeCapIsNotReachedByAffiliate($carrier, $constraint);
            }

            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->ensureVisitCapIsNotReached($carrier, $constraint);
            }
        }
    }

    /**
     * @param Carrier       $carrier
     * @param Campaign|null $campaign
     *
     * @return bool
     */
    public function isLandingDisabled(Carrier $carrier, Campaign $campaign = null): bool
    {
        try {
            $isSupportRequestedFlow = $this->oneClickFlowChecker->check($carrier->getBillingCarrierId(), OneClickFlowParameters::LP_OFF);
            $this->logger->debug('Inside isLandingDisabled()', [
                '$isSupportRequestedFlow' => $isSupportRequestedFlow,
                'isOneClickFlow'          => $carrier->isOneClickFlow(),
            ]);

            if ($carrier->isOneClickFlow() && $isSupportRequestedFlow) {

                if ($campaign) {
                    /** @var Affiliate $affiliate */
                    $affiliate          = $campaign->getAffiliate();
                    $isLPOffByAffiliate = $affiliate->isOneClickFlow() && ($affiliate->hasCarrier($carrier) || empty($affiliate->getCarriers()));

                    $isCampaignScheduleExistAndTriggered = $campaign->getSchedule()->isEmpty()
                        ? true
                        : $this->campaignScheduleRepository->isNowInSchedule($campaign);

                    $isLPOffByCampaign = $campaign->isOneClickFlow() && $isCampaignScheduleExistAndTriggered;

                    $this->logger->debug('Inside isLandingDisabled() campaign check', [
                        '$isLPOffByCampaign'               => $isLPOffByCampaign,
                        '$isLPOffByAffiliate'              => $isLPOffByAffiliate,
                        '$affiliate->hasCarrier($carrier)' => $affiliate->hasCarrier($carrier),
                        '$affiliate->getCarriers()'        => var_dump($affiliate->getCarriers())
                    ]);
                    return $isLPOffByAffiliate && $isLPOffByCampaign;
                }

                return true;
            }

            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param Carrier               $carrier
     * @param ConstraintByAffiliate $constraint
     *
     * @throws SubscriptionCapReachedOnAffiliate
     */
    private function ensureSubscribeCapIsNotReachedByAffiliate(
        Carrier $carrier,
        ConstraintByAffiliate $constraint
    ): void
    {
        if ($this->carrierCapChecker->isCapReachedForAffiliate($constraint)) {
            $this->logger->debug('CAP checking on LP', [
                'message'    => 'Constraint by aff say: isCapReachedForAffiliate = true',
                'constraint' => $constraint
            ]);
            throw new SubscriptionCapReachedOnAffiliate($constraint, $carrier);
        }
    }

    /**
     * @param Carrier               $carrier
     * @param ConstraintByAffiliate $constraint
     *
     * @throws \SubscriptionBundle\CAPTool\Exception\VisitCapReached
     */
    private function ensureVisitCapIsNotReached(Carrier $carrier, ConstraintByAffiliate $constraint): void
    {
        if (!$this->visitConstraintByAffiliate->canVisit($carrier, $constraint)) {
            $this->logger->debug('CAP checking on LP', [
                'message'    => 'Constraint by aff say: CanVisit = false',
                'constraint' => $constraint
            ]);
            throw new VisitCapReached($constraint);
        }
    }

    /**
     * @param Carrier $carrier
     *
     * @throws SubscriptionCapReachedOnCarrier
     */
    private function ensureSubscribeCapIsNotReachedByCarrier(Carrier $carrier): void
    {
        if ($this->carrierCapChecker->isCapReachedForCarrier($carrier)) {
            $this->logger->debug('CAP checking on LP', ['message' => 'isCapReachedForCarrier say: isCapReachedForCarrier = true']);
            throw new SubscriptionCapReachedOnCarrier($carrier);
        }
    }

    public function ensureCanAccessByVisits(CampaignInterface $campaign, Carrier $carrier): void
    {
        $affiliate = $campaign->getAffiliate();

        foreach ($affiliate->getConstraints() as $constraint) {
            /** @var ConstraintByAffiliate $constraint */
            if ($carrier && $carrier->getUuid() !== $constraint->getCarrier()->getUuid()) {
                continue;
            }

            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->ensureVisitCapIsNotReached($carrier, $constraint);
            }
        }
    }

    public function isAffiliatePublisherBanned(Request $request, CampaignInterface $campaign): bool
    {
        $affiliate = $campaign->getAffiliate();
        return $this->affiliateBannedPublisherChecker->isPublisherBanned($affiliate, $request->query->all());
    }
}