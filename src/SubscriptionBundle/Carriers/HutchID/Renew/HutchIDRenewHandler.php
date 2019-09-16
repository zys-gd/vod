<?php


namespace SubscriptionBundle\Carriers\HutchID\Renew;


use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Renew\Handler\HasCommonFlow;

class HutchIDRenewHandler implements HasCommonFlow
{
    /**
     * @var Notifier
     */
    private $notifier;

    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @param Subscription $subscription
     * @param int          $processId
     *
     * @throws \SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException
     */
    public function onRenewSendSuccess(Subscription $subscription, int $processId): void
    {
        $this->notifier->sendNotification(
            'notify_renew',
            $subscription,
            $subscription->getSubscriptionPack(),
            $subscription->getSubscriptionPack()->getCarrier()
        );
    }

    /**
     * @param Subscription $subscription
     * @param string       $errorText
     *
     * @throws \SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException
     */
    public function onRenewSendFailure(Subscription $subscription, string $errorText): void
    {
        if ($subscription->isNotEnoughCredit()) {
            $this->notifier->sendNotification(
                'failed_renew',
                $subscription,
                $subscription->getSubscriptionPack(),
                $subscription->getSubscriptionPack()->getCarrier()
            );
        }
    }
}