<?php


namespace SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\Exception\NotificationSendFailedException;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\UnsubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\FakeResponseProvider;
use SubscriptionBundle\Subscription\Common\SubscriptionSerializer;
use SubscriptionBundle\Subscription\Notification\Notifier;

class SendUnsubscribeNotificationPerformer
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
        $this->logger = $logger;
        $this->notifier = $notifier;
        $this->fakeResponseProvider = $fakeResponseProvider;
        $this->subscriptionSerializer = $subscriptionSerializer;
    }

    /**
     * @param Subscription $subscription
     * @return void
     * @throws \SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException
     */
    public function doSentNotification(Subscription $subscription): void
    {
        $carrier = $subscription->getUser()->getCarrier();

        try {
            $this->notifier->sendNotification(
                UnsubscribeProcess::PROCESS_METHOD_UNSUBSCRIBE,
                $subscription,
                $subscription->getSubscriptionPack(),
                $carrier
            );

        } catch (NotificationSendFailedException $e) {
            $this->logger->error($e->getMessage(), [
                'subscription' => $this->subscriptionSerializer->serializeShort($subscription)
            ]);
            throw new UnsubscribingProcessException('Error while trying to unsubscribe (sending notification)');
        }
    }
}