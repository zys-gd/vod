<?php

namespace App\Domain\ACL\Exception;

use App\Domain\Entity\Carrier;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class SubscriptionCapReachedOnAffiliate
 */
class SubscriptionCapReachedOnAffiliate extends AccessException
{
    /**
     * @var ConstraintByAffiliate
     */
    private $constraint;

    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * SubscriptionCapReachedOnAffiliate constructor.
     * @param ConstraintByAffiliate $constraint
     * @param Carrier               $carrier
     */
    public function __construct(ConstraintByAffiliate $constraint, Carrier $carrier)
    {
        $this->constraint = $constraint;
        $this->carrier    = $carrier;
    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getConstraint(): ConstraintByAffiliate
    {
        return $this->constraint;
    }

    /**
     * @return Carrier
     */
    public function getCarrier(): Carrier
    {
        return $this->carrier;
    }
}