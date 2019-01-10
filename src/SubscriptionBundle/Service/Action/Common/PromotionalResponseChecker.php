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

    const MISSING_PROMOTIONAL_TIER = 0;

    public function isPromotionalResponseNeeded(Subscription $subscription)
    {
        $isProviderManaged = $subscription->getSubscriptionPack()->isProviderManagedSubscriptions();

        return $this->isSubscriptionPromotionalTierMissing($subscription) && !$isProviderManaged;

    }

    private function isSubscriptionPromotionalTierMissing(Subscription $subscription): bool
    {
        return $subscription->getPromotionTierId() === self::MISSING_PROMOTIONAL_TIER;
    }
}