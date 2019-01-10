<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 14:11
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Common;


use SubscriptionBundle\Entity\Subscription;

class SubscriptionEligibilityChecker
{

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function isEligibleToSubscribe(Subscription $subscription): bool
    {
        if ($subscription->isActive()) {
            return false;
        }

        if ($subscription->isPending()) {
            return false;
        }

        return true;
    }


}