<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.05.19
 * Time: 13:53
 */

namespace SubscriptionBundle\Service\VisitCAPTool;


class KeyGenerator
{

    public function generateVisitKey(
        \IdentificationBundle\Entity\CarrierInterface $carrier,
        \SubscriptionBundle\Entity\Affiliate\AffiliateInterface $affiliate
    )
    {
        return sprintf('visit-%s-%s', $carrier->getBillingCarrierId(), $affiliate->getUuid());
    }
}