<?php

namespace SubscriptionBundle\Subscription\Common;

use SubscriptionBundle\Entity\Subscription;

/**
 * Class SendNotificationChecker
 */
class SendNotificationChecker
{
    /**
     * @param Subscription $subscription
     *
     * @return bool
     */
    public function isNotificationShouldBeSent(Subscription $subscription): bool
    {
        $isProviderManaged = $subscription->getSubscriptionPack()->isProviderManagedSubscriptions();

        return !$isProviderManaged;
    }
}