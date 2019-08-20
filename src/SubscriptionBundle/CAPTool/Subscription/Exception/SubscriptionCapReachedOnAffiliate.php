<?php

namespace SubscriptionBundle\CAPTool\Subscription\Exception;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class SubscriptionCapReachedOnAffiliate
 */
class SubscriptionCapReachedOnAffiliate extends CapToolAccessException
{
    /**
     * @var ConstraintByAffiliate
     */
    private $constraint;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * SubscriptionCapReachedOnAffiliate constructor.
     * @param ConstraintByAffiliate $constraint
     * @param CarrierInterface      $carrier
     */
    public function __construct(ConstraintByAffiliate $constraint, CarrierInterface $carrier)
    {
        $this->constraint = $constraint;
        $this->carrier    = $carrier;

        parent::__construct();
    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getConstraint(): ConstraintByAffiliate
    {
        return $this->constraint;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier(): CarrierInterface
    {
        return $this->carrier;
    }
}