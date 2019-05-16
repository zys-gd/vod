<?php

namespace App\Domain\ACL\Exception;

use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class VisitCapReached
 */
class VisitCapReached extends AccessException
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
    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getConstraint(): ConstraintByAffiliate
    {
        return $this->constraint;
    }
}