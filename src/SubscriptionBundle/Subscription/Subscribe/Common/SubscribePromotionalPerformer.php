<?php

namespace SubscriptionBundle\Subscription\Subscribe\Common;

use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\Exception\NotificationSendFailedException;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\FakeResponseProvider;
use SubscriptionBundle\Subscription\Common\SubscriptionSerializer;
use SubscriptionBundle\Subscription\Notification\Notifier;

/**
 * Class SubscribePromotionalPerformer
 * @package SubscriptionBundle\Subscription\Subscribe\Common
 */
class SubscribePromotionalPerformer
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Notifier */
    private $notifier;

    /** @var FakeResponseProvider */
    private $fakeResponseProvider;

    /** @var SubscriptionSerializer */
    private $subscriptionSerializer;

    /**
     * SubscribePerformer constructor.
     * @param LoggerInterface                                                $logger
     * @param Notifier                                                       $notifier
     * @param FakeResponseProvider                                           $fakeResponseProvider
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionSerializer $subscriptionSerializer
     */
    public function __construct(
        LoggerInterface $logger,
        Notifier $notifier,
        FakeResponseProvider $fakeResponseProvider,
        SubscriptionSerializer $subscriptionSerializer
    )
    {
        $this->logger                 = $logger;
        $this->notifier               = $notifier;
        $this->fakeResponseProvider   = $fakeResponseProvider;
        $this->subscriptionSerializer = $subscriptionSerializer;
    }

    /**
     * @param Subscription $subscription
     *
     * @return ProcessResult
     */
    public function doSubscribe(Subscription $subscription): ProcessResult
    {

        $carrier = $subscription->getUser()->getCarrier();

        try {
            $this->notifier->sendNotification(
                SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                $subscription,
                $subscription->getSubscriptionPack(),
                $carrier
            );

        } catch (NotificationSendFailedException $e) {

            $this->logger->error($e->getMessage(), [
                'subscription' => $this->subscriptionSerializer->serializeShort($subscription)
            ]);

            throw new SubscribingProcessException('Error while trying to subscribe (sending notification)', 0, $e, null, null, 'subscription_notification');
        }

        return $this->fakeResponseProvider->getDummyResult(
            $subscription,
            SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
            ProcessResult::STATUS_SUCCESSFUL
        );

    }
}