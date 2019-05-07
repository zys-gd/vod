<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Common;

use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Service\Action\Subscribe\SubscribeParametersProvider;

/**
 * Class SubscribePerformer
 * @package SubscriptionBundle\Service\Action\Subscribe\Common
 */
class SubscribePerformer
{
    /** @var SubscribeProcess */
    private $subscribeProcess;

    /** @var SubscribeParametersProvider */
    private $subscribeParametersProvider;

    /**
     * SubscribePerformer constructor.
     * @param SubscribeProcess $subscribeProcess
     * @param SubscribeParametersProvider $subscribeParametersProvider
     */
    public function __construct(
        SubscribeProcess $subscribeProcess,
        SubscribeParametersProvider $subscribeParametersProvider
    ) {
        $this->subscribeParametersProvider = $subscribeParametersProvider;
        $this->subscribeProcess = $subscribeProcess;
    }

    public function doSubscribe(Subscription $subscription, $additionalData):ProcessResult
    {
        $parameters = $this->subscribeParametersProvider->provideParameters($subscription, $additionalData);
        return $this->subscribeProcess->doSubscribe($parameters);
    }
}