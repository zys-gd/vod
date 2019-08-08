<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.08.19
 * Time: 14:19
 */

namespace SubscriptionBundle\Service\VisitCAPTool;


use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class ConstraintAvailabilityChecker
{
    public function isCapEnabledForAffiliate(AffiliateInterface $affiliate): bool
    {
        foreach ($affiliate->getConstraints() as $constraint) {

            /** @var ConstraintByAffiliate $constraint */
            if ($constraint->getCapType() == ConstraintByAffiliate::CAP_TYPE_VISIT) {
                return true;
            }
        }

        return false;
    }
}