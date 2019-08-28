<?php

namespace SubscriptionBundle\Subscription\Subscribe\Common;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\SubscribeParametersProvider;

/**
 * Class SubscribePerformer
 * @package SubscriptionBundle\Subscription\Subscribe\Common
 */
class SubscribePerformer
{
    /** @var SubscribeProcess */
    private $subscribeProcess;

    /** @var SubscribeParametersProvider */
    private $subscribeParametersProvider;

    /**
     * SubscribePerformer constructor.
     * @param SubscribeProcess            $subscribeProcess
     * @param SubscribeParametersProvider $subscribeParametersProvider
     */
    public function __construct(
        SubscribeProcess $subscribeProcess,
        SubscribeParametersProvider $subscribeParametersProvider
    )
    {
        $this->subscribeParametersProvider = $subscribeParametersProvider;
        $this->subscribeProcess            = $subscribeProcess;
    }

    public function doSubscribe(Subscription $subscription, $additionalData): ProcessResult
    {
        $parameters = $this->subscribeParametersProvider->provideParameters($subscription, $additionalData);
        return $this->subscribeProcess->doSubscribe($parameters);
    }
}