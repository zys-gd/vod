<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:06
 */

namespace SubscriptionBundle\Subscription\Common;


use SubscriptionBundle\Entity\Subscription;

class SendNotificationChecker
{


    public function isNotificationShouldBeSent(Subscription $subscription)
    {
        $isProviderManaged = $subscription->getSubscriptionPack()->isProviderManagedSubscriptions();

        return !$isProviderManaged;

    }

}