<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.19
 * Time: 16:15
 */

namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class StorageKeyGenerator
{
    /**
     * @param CarrierInterface $carrier
     * @return string
     */
    public function generateKey(CarrierInterface $carrier): string
    {
        $keyParts   = [];
        $keyParts[] = $carrier->getBillingCarrierId();
        $key        = implode('_', $keyParts);
        return $key;
    }

    public function generateAffiliateConstraintKey(ConstraintByAffiliate $affiliateConstant): string
    {
        $keyParts   = [];
        $keyParts[] = $affiliateConstant->getUuid();
        $key        = implode('_', $keyParts);
        return $key;
    }
}