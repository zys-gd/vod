<?php

namespace SubscriptionBundle\Subscription\Reminder\Service;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Notification\Common\MessageCompiler;
use SubscriptionBundle\Subscription\Reminder\DTO\RemindSettings;
use SubscriptionBundle\Subscription\Reminder\DTO\SendRemindersResult;

/**
 * Class Reminder
 */
class Reminder
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        RequestSender $requestSender,
        MessageCompiler $messageCompiler
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param CarrierInterface $carrier
     * @param RemindSettings   $remindSettings
     *
     * @return SendRemindersResult
     */
    public function doRemind(CarrierInterface $carrier, RemindSettings $remindSettings): SendRemindersResult
    {
        $subscriptions = $this
            ->subscriptionRepository
            ->findReminderSubscriptions($carrier, $remindSettings->getDaysInterval());

        if (empty($subscriptions)) {
            return new SendRemindersResult();
        }


    }
}