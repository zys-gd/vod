<?php

namespace App\Domain\ACL;

use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Exception\AffiliateConstraintAccessException;
use App\Domain\ACL\Exception\CampaignAccessException;
use App\Domain\ACL\Exception\CampaignPausedException;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

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
     * LandingPageAccessResolver constructor
     *
     * @param VisitConstraintByAffiliate $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign    $visitAccessorByCampaign
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign
    )
    {
        $this->visitConstraintByAffiliate = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign    = $visitAccessorByCampaign;
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

        $affiliate = $campaign->getAffiliate();
        /** @var ConstraintByAffiliate $constraint */
        foreach ($affiliate->getConstraints() as $constraint) {
            if ($carrier && $carrier->getUuid() !== $constraint->getCarrier()->getUuid()) {
                continue;
            }

            if ($constraint->getCapType() !== ConstraintByAffiliate::CAP_TYPE_VISIT) {
                continue;
            }

            if (!$this->visitConstraintByAffiliate->canVisit($carrier, $constraint)) {
                throw new AffiliateConstraintAccessException($constraint);
            }
        }
    }
}