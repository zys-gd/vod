<?php

namespace SubscriptionBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use SubscriptionBundle\Entity\Subscription;

class SubscriptionGameDownloadEvent extends Event
{

    const EVENT_NAME = 'subscription.game_download';

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * SubscriptionOnHoldEvent constructor.
     * @param Subscription $subscription
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }


}