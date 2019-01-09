<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.10.18
 * Time: 11:43
 */

namespace SubscriptionBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use SubscriptionBundle\Entity\Subscription;

class SubscriptionOnHoldEvent extends Event
{

    const EVENT_NAME = 'subscription.on_hold';

    /**
     * @var Subscription
     */
    private $subscription;


    /**
     * SubscriptionOnHoldEvent constructor.
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