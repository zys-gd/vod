<?php

namespace App\Domain\ACL;

use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CarrierRepository;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Exception\CampaignAccessException;
use App\Domain\ACL\Exception\CampaignPausedException;
use App\Domain\ACL\Exception\SubscriptionCapReachedOnAffiliate;
use App\Domain\ACL\Exception\SubscriptionCapReachedOnCarrier;
use App\Domain\ACL\Exception\VisitCapReached;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CAPTool\Limiter\SubscriptionCapChecker;

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
     * LandingPageAccessResolver constructor
     *
     * @param VisitConstraintByAffiliate $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign    $visitAccessorByCampaign
     * @param CarrierRepository          $carrierRepository
     * @param CampaignRepository         $campaignRepository
     * @param SessionInterface           $session
     * @param SubscriptionCapChecker     $subscriptionCapChecker
     * @param LoggerInterface            $logger
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        SessionInterface $session
        SubscriptionCapChecker $subscriptionCapChecker,
        LoggerInterface $logger
    )
    {
        $this->visitConstraintByAffiliate = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign    = $visitAccessorByCampaign;
        $this->carrierCapChecker          = $subscriptionCapChecker;
        $this->logger                     = $logger;
        $this->carrierRepository          = $carrierRepository;
        $this->campaignRepository         = $campaignRepository;
        $this->session                    = $session;
    }

    /**
     * @param Campaign $campaign
     * @param Carrier  $carrier
     *
     * @return void
     */
    public function ensureCanAccess(Campaign $campaign, Carrier $carrier): void
    {
        if ($campaign->getIsPause()) {
            $this->logger->debug('Campaign on pause');
            throw new CampaignPausedException();
        }

        if (!$this->visitAccessorByCampaign->canVisit($campaign, $carrier)) {
            $this->logger->debug('visitAccessorByCampaign say: cant visit');
            throw new CampaignAccessException($campaign);
        }

        if ($this->carrierCapChecker->isCapReachedForCarrier($carrier)) {
            $this->logger->debug('isCapReachedForCarrier say: isCapReachedForCarrier = true');
            throw new SubscriptionCapReachedOnCarrier($carrier);
        }

        $affiliate = $campaign->getAffiliate();

        foreach ($affiliate->getConstraints() as $constraint) {
            /** @var ConstraintByAffiliate $constraint */
            if ($carrier && $carrier->getUuid() !== $constraint->getCarrier()->getUuid()) {
                continue;
            }

            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE) {
                if ($this->carrierCapChecker->isCapReachedForAffiliate($constraint)) {
                    $this->logger->debug('Constraint by aff say: isCapReachedForAffiliate = true', [
                        'constraint' => $constraint
                    ]);
                    throw new SubscriptionCapReachedOnAffiliate($constraint, $carrier);
                }
            }

            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_VISIT) {
                if (!$this->visitConstraintByAffiliate->canVisit($carrier, $constraint)) {
                    $this->logger->debug('Constraint by aff say: CanVisit = false', [
                        'constraint' => $constraint
                    ]);
                    throw new VisitCapReached($constraint);
                }
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isLandingDisabled(Request $request): bool
    {
        try {
            $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
            $campaignToken    = $request->get('cid', '');
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);

            /** @var Campaign $campaign */
            $campaign = $this->campaignRepository->findOneBy(['campaignToken' => $campaignToken]);
            // $campaign->getAffiliate()
            return $carrier->isLpOff() || $campaign->isLpOff() || $campaign->getAffiliate()->isLpOff();
        } catch (\Throwable $e) {
            return false;
        }
    }
}