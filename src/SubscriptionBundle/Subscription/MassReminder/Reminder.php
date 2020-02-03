<?php

namespace SubscriptionBundle\Subscription\MassReminder;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\UuidGenerator;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionReminder;
use SubscriptionBundle\Repository\SubscriptionReminderRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Reminder\DTO\RemindSettings;
use SubscriptionBundle\Subscription\Reminder\DTO\SendRemindersResult;
use SubscriptionBundle\Subscription\Reminder\Service\RemindSender;

/**
 * Class Reminder
 */
class Reminder
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CommonFlowHandler constructor.
     *
     * @param SubscriptionRepository         $subscriptionRepository
     * @param SubscriptionReminderRepository $subscriptionReminderRepository
     * @param RemindSender                   $remindSender
     * @param EntityManagerInterface         $entityManager
     */
    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        SubscriptionReminderRepository $subscriptionReminderRepository,
        RemindSender $remindSender,
        EntityManagerInterface $entityManager
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->remindSender = $remindSender;
        $this->subscriptionReminderRepository = $subscriptionReminderRepository;
        $this->entityManager = $entityManager;
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
            ->findSubscriptionsForRemind($carrier, $remindSettings->getDaysInterval());

        $succeeded = [];
        $failed = [];

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $isSuccess = $this->remindSender->send(
                $subscription->getUser(),
                $subscription->getSubscriptionPack(),
                $subscription,
                $remindSettings->getBody()
            );

            if ($isSuccess) {
                $succeeded[] = $subscription;
            } else {
                $failed[] = $subscription;
            }
        }

        $result = new SendRemindersResult($succeeded, $failed);

        $this->subscriptionReminderRepository->deleteBySubscriptions($result->getSucceededSubscriptions());

        /** @var Subscription $subscription */
        foreach ($result->getSucceededSubscriptions() as $subscription) {
            $reminder = (new SubscriptionReminder(UuidGenerator::generate()))->setSubscription($subscription);

            $this->entityManager->persist($reminder);
        }

        $this->entityManager->flush();

        return $result;
    }
}