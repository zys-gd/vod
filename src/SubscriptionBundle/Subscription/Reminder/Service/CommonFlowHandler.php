<?php

namespace SubscriptionBundle\Subscription\Reminder\Service;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Repository\SubscriptionReminderRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Reminder\DTO\RemindSettings;
use SubscriptionBundle\Subscription\Reminder\DTO\SendRemindersResult;

/**
 * Class Reminder
 */
class CommonFlowHandler
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var RemindSender
     */
    private $remindSender;

    /**
     * @var SubscriptionReminderRepository
     */
    private $subscriptionReminderRepository;

    /**
     * CommonFlowHandler constructor.
     *
     * @param SubscriptionRepository         $subscriptionRepository
     * @param SubscriptionReminderRepository $subscriptionReminderRepository
     * @param RemindSender                   $remindSender
     */
    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        SubscriptionReminderRepository $subscriptionReminderRepository,
        RemindSender $remindSender
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->remindSender = $remindSender;
        $this->subscriptionReminderRepository = $subscriptionReminderRepository;
    }

    /**
     * @param CarrierInterface $carrier
     * @param RemindSettings   $remindSettings
     *
     * @return SendRemindersResult
     *
     * @throws \Exception
     */
    public function doRemind(CarrierInterface $carrier, RemindSettings $remindSettings): SendRemindersResult
    {
        $subscriptions = $this
            ->subscriptionRepository
            ->findReminderSubscriptions($carrier, $remindSettings->getDaysInterval());

        $result = new SendRemindersResult();

        if (empty($subscriptions)) {
            return $result;
        }

        foreach ($subscriptions as $subscription) {
            $isSuccess = $this->remindSender->send($subscription, $remindSettings->getBody());

            if ($isSuccess) {
                $result->addSuccessSubscription($subscription);
            } else {
                $result->addFailedSubscription($subscription);
            }
        }

        $this->subscriptionReminderRepository->updateSentDateBySubscriptions($result->getSucceededSubscriptions());

        return $result;
    }
}