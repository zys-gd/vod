<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:06
 */

namespace SubscriptionBundle\Service\Action\Common;


use SubscriptionBundle\Entity\Subscription;

class PromotionalResponseChecker
{


    public function isPromotionalResponseNeeded(Subscription $subscription)
    {
        $isProviderManaged = $subscription->getSubscriptionPack()->isProviderManagedSubscriptions();

        return !$isProviderManaged;

    }

}