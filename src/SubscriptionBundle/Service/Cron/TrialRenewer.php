<?php

namespace SubscriptionBundle\Service\Cron;


use App\Domain\Entity\Carrier;
use SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Service\Action\Renew\Renewer;
use SubscriptionBundle\Service\Action\Subscribe\Subscriber;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use SubscriptionBundle\Service\SubscriptionProvider;

/**
 * Renew trial subscriptions for carriers, whom trial period is greater than their renew period
 * (eg. Nazara have 3 day trial period (2 games/day) and subscription period is 1 day)
 *
 * @package SubscriptionBundle\Services
 */
class TrialRenewer
{

    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var Renewer
     */
    private $renewer;
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;


    /**
     * TrialRenewService constructor.
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param Renewer $renewer
     * @param SubscriptionProvider $subscriptionProvider
     */
    public function __construct(
        SubscriptionPackProvider $subscriptionPackProvider,
        Renewer $renewer,
        SubscriptionProvider $subscriptionProvider
    )
    {
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->renewer               = $renewer;
        $this->subscriptionProvider     = $subscriptionProvider;
    }

    /**
     * @param Carrier $carrier
     * @return array
     */
    public function renew(Carrier $carrier): array
    {
        $logRows = [];

        $subscriptionsToRenew = $this->subscriptionProvider->getTrialSubscriptionsToRenew($carrier);

        foreach ($subscriptionsToRenew as $subscription) {
            $billableUser = $subscription->getUser();
            try {
                $pack = $this->subscriptionPackProvider->getActiveSubscriptionPack($billableUser);
            } catch (ActiveSubscriptionPackNotFound $e) {
                $logRows[] = $e->getMessage();
                continue;
            }

            try {
                $this->renewer->subscribe($billableUser, $pack);
            } catch (\Exception $e) {
                $logRows[] = $e->getMessage();
            }
            $logRows[] = 'Subscription ' . $subscription->getUuid() . ' is renewed';
        }

        $logRows[] = sprintf('Trial renew for %s is finished', $carrier->getName());

        return $logRows;
    }
}