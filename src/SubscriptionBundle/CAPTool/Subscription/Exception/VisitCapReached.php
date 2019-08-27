<?php

namespace SubscriptionBundle\CAPTool\Subscription\Exception;

use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class VisitCapReached
 */
class VisitCapReached extends CapToolAccessException
{
    /**
     * @var ConstraintByAffiliate
     */
    private $constraint;

    /**
     * VisitCapReached constructor.
     * @param ConstraintByAffiliate $constraint
     */
    public function __construct(ConstraintByAffiliate $constraint)
    {
        $this->constraint = $constraint;

        parent::__construct();

    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getConstraint(): ConstraintByAffiliate
    {
        return $this->constraint;
    }
}