<?php


namespace SubscriptionBundle\Carriers\HutchID\Subscribe;

use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Subscription\Callback\Common\Handler\SubscriptionCallbackHandler;

class HutchIDSMSSubscriber
{
    /**
     * @var SubscriptionCallbackHandler
     */
    private $subscriptionCallbackHandler;

    public function __construct(SubscriptionCallbackHandler $subscriptionCallbackHandler)
    {
        $this->subscriptionCallbackHandler = $subscriptionCallbackHandler;
    }

    public function subscribe(Subscription $subscription, ProcessResult $processResponse)
    {
        $this->subscriptionCallbackHandler->doProcess($subscription, $processResponse);
    }
}