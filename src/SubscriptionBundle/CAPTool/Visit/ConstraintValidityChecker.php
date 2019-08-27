<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 14:16
 */

namespace SubscriptionBundle\CAPTool\Visit;


use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class ConstraintValidityChecker
{
    public function isValidConstraint(ConstraintByAffiliate $constraintByAffiliate): bool
    {
        return $constraintByAffiliate->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT;
    }
}