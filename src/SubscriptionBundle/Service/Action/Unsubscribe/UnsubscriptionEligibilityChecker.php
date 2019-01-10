<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 18:23
 */

namespace SubscriptionBundle\Service\Action\Unsubscribe;


use SubscriptionBundle\Entity\Subscription;

class UnsubscriptionEligibilityChecker
{

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function isEligibleToUnsubscribe(Subscription $subscription): bool
    {
        if ($subscription->isInActive()) {
            return false;
        }
        if ($subscription->isPending()) {
            return false;
        }

        return true;


    }
}