<?php

namespace App\Domain\ACL\Accessors;

use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Affiliate\CapConstraint\VisitChecker;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class VisitConstraintByAffiliateService
 */
class VisitConstraintByAffiliate
{

    /**
     * @var VisitChecker
     */
    private $checker;

    /**
     * AbstractConstraintByAffiliateService constructor
     *
     * @param VisitChecker $checker
     */
    public function __construct(
        VisitChecker $checker
    )
    {
        $this->checker                = $checker;
    }

    /**
     * @param Campaign         $campaign
     *
     * @param CarrierInterface $carrier
     * @return bool
     *
     */
    public function canVisit(Campaign $campaign, CarrierInterface $carrier): bool
    {
        $affiliate = $campaign->getAffiliate();

        /** @var ConstraintByAffiliate $constraint */
        foreach ($affiliate->getConstraints()->getIterator() as $constraint) {
            if ($carrier && $carrier->getUuid() !== $constraint->getCarrier()->getUuid()) {
                continue;
            }

            if ($this->checker->isCapReached($carrier, $constraint)) {
                return false;
            }
        }

        return true;
    }

}