<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:42
 */

namespace SubscriptionBundle\Affiliate\Service;


use SubscriptionBundle\Entity\Affiliate\AffiliateLog;

class AffiliateLogFactory
{
    public function create(): AffiliateLog
    {

        $affiliateLog = new AffiliateLog();

        return $affiliateLog;
    }

}