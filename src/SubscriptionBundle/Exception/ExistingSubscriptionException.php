<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 14/08/17
 * Time: 5:03 PM
 */

namespace SubscriptionBundle\Exception;


use SubscriptionBundle\Entity\Subscription;

class ExistingSubscriptionException extends SubscriptionException
{

    /**
     * @var Subscription
     */
    private $subscription;

    public function __construct(string $message = "", Subscription $subscription)
    {
        parent::__construct($message, 0, null);
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