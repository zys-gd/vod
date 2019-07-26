<?php

namespace SubscriptionBundle\Subscription\Renew\Service;


use App\Domain\Entity\Carrier;
use SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Subscription\Renew\Renewer;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;

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
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionExtractor
     */
    private $subscriptionProvider;


    /**
     * TrialRenewService constructor.
     *
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param Renewer                  $renewer
     * @param SubscriptionExtractor    $subscriptionProvider
     */
    public function __construct(
        SubscriptionPackProvider $subscriptionPackProvider,
        Renewer $renewer,
        \SubscriptionBundle\Subscription\Common\SubscriptionExtractor $subscriptionProvider
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
            $User = $subscription->getUser();
            try {
                $pack = $this->subscriptionPackProvider->getActiveSubscriptionPack($User);
            } catch (ActiveSubscriptionPackNotFound $e) {
                $logRows[] = $e->getMessage();
                continue;
            }

            try {
                $this->renewer->subscribe($User, $pack);
            } catch (\Exception $e) {
                $logRows[] = $e->getMessage();
            }
            $logRows[] = 'Subscription ' . $subscription->getUuid() . ' is renewed';
        }

        $logRows[] = sprintf('Trial renew for %s is finished', $carrier->getName());

        return $logRows;
    }
}