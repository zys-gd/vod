<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.09.19
 * Time: 15:16
 */

namespace SubscriptionBundle\API\Service;


use SubscriptionBundle\Entity\Subscription;

class LegacyBillingFormatter
{
    public function getFormattedData(Subscription $subscription): array
    {

        return [
            'subscription_state' => $this->getSubscriptionState($subscription),
            'added_at'           => $subscription->getCreated()
                ? $subscription->getCreated()->format('Y-m-d H:i:s')
                : ''
        ];


    }

    private function getSubscriptionState(Subscription $subscription): string
    {
        $currentStage = $subscription->getCurrentStage();
        if (Subscription::ACTION_SUBSCRIBE === $currentStage) {
            if ($subscription->isNotEnoughCredit()) {
                return 'UNSUBSCRIBE';
            }

            return 'SUBSCRIBE';
        }
        if (Subscription::ACTION_RENEW === $currentStage) {
            return 'RENEW';
        }
        if (Subscription::ACTION_UNSUBSCRIBE === $currentStage) {
            return 'UNSUBSCRIBE';
        }

        return 'UNSUBSCRIBE';
    }
}