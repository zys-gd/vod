<?php

namespace App\Domain\ACL;

use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Exception\CampaignAccessException;
use App\Domain\ACL\Exception\CampaignPausedException;
use App\Domain\ACL\Exception\SubscriptionCapReachedOnAffiliate;
use App\Domain\ACL\Exception\SubscriptionCapReachedOnCarrier;
use App\Domain\ACL\Exception\VisitCapReached;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
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
     * LandingPageAccessResolver constructor
     *
     * @param VisitConstraintByAffiliate $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign    $visitAccessorByCampaign
     * @param SubscriptionCapChecker     $subscriptionCapChecker
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        SubscriptionCapChecker $subscriptionCapChecker
    )
    {
        $this->visitConstraintByAffiliate = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign    = $visitAccessorByCampaign;
        $this->carrierCapChecker          = $subscriptionCapChecker;
    }

    /**
     * @param Campaign $campaign
     * @param Carrier  $carrier
     * @return void
     */
    public function ensureCanAccess(Campaign $campaign, Carrier $carrier): void
    {
        if ($campaign->getIsPause()) {
            throw new CampaignPausedException();
        }

        if (!$this->visitAccessorByCampaign->canVisit($campaign, $carrier)) {
            throw new CampaignAccessException($campaign);
        }

        if ($this->carrierCapChecker->isCapReachedForCarrier($carrier)) {
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
                    throw new SubscriptionCapReachedOnAffiliate($constraint, $carrier);
                }
            }

            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_VISIT) {
                if (!$this->visitConstraintByAffiliate->canVisit($carrier, $constraint)) {
                    throw new VisitCapReached($constraint);
                }
            }
        }
    }
}