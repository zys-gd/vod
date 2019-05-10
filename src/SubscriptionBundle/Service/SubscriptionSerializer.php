<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.04.19
 * Time: 15:04
 */

namespace SubscriptionBundle\Service;


use SubscriptionBundle\Entity\Subscription;

class SubscriptionSerializer
{
    public function serializeShort(Subscription $subscription): array
    {
        return [
            'uuid'             => $subscription->getUuid(),
            'status'           => $subscription->getStatus(),
            'stage'            => $subscription->getCurrentStage(),
            'credits'          => $subscription->getCredits(),
            'subscriptionPack' => [
                'carrierId' => $subscription->getSubscriptionPack()->getCarrier()->getBillingCarrierId(),
            ]
        ];
    }
}