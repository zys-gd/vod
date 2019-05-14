<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 16:26
 */

namespace App\Domain\ACL\Exception;


use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class AffiliateConstraintAccessException extends AccessException
{
    /**
     * @var ConstraintByAffiliate
     */
    private $constraint;


    /**
     * AffiliateConstraintAccessException constructor.
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