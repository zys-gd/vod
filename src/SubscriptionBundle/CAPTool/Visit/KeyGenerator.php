<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.05.19
 * Time: 13:53
 */

namespace SubscriptionBundle\CAPTool\Visit;


class KeyGenerator
{

    public function generateVisitKey(
        \CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier,
        \SubscriptionBundle\Entity\Affiliate\AffiliateInterface $affiliate
    )
    {
        return sprintf('%s-%s', $affiliate->getUuid(), $carrier->getBillingCarrierId());
    }
}