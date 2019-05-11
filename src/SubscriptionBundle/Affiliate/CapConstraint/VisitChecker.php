<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.05.19
 * Time: 13:34
 */

namespace SubscriptionBundle\Affiliate\CapConstraint;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class VisitChecker
{
    public function isCapReached(CarrierInterface $carrier, ConstraintByAffiliate $constraintByAffiliate): bool
    {

    }
}