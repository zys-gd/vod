<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:24
 */

namespace SubscriptionBundle\Affiliate\Service;


use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Subscription;

class AffiliateSender
{
    public function checkAffiliateEligibilityAndSendEvent(Subscription $subscription, UserInfo $userInfo)
    {
        // todo implement actual tracking
    }
}