<?php


namespace SubscriptionBundle\Subscription\SubscribeBack;


use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionCreator;

class Subscriber
{
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var SubscriptionCreator
     */
    private $subscriptionCreator;
    /**
     * @var OnSubscribeUpdater
     */
    private $subscribeUpdater;
    /**
     * @var DataProvider
     */
    private $dataProvider;

    public function __construct(
        EntitySaveHelper $entitySaveHelper,
        SubscriptionFactory $subscriptionCreator,
        OnSubscribeUpdater $subscribeUpdater,
        DataProvider $dataProvider
    )
    {
        $this->entitySaveHelper    = $entitySaveHelper;
        $this->subscriptionCreator = $subscriptionCreator;
        $this->subscribeUpdater    = $subscribeUpdater;
        $this->dataProvider        = $dataProvider;
    }

    /**
     * @param User             $user
     * @param SubscriptionPack $subscriptionPack
     * @param string           $billingProcessId
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function subscribe(User $user, SubscriptionPack $subscriptionPack, string $billingProcessId): array
    {
        $processResult   = $this->dataProvider->getProcessData($billingProcessId);
        $newSubscription = $this->createPendingSubscription($user, $subscriptionPack);

        $this->subscribeUpdater->updateSubscriptionByResponse($newSubscription, $processResult);

        $this->entitySaveHelper->persistAndSave($user);
        $this->entitySaveHelper->persistAndSave($newSubscription);

        return [$newSubscription, $processResult];
    }

    /**
     * @param User             $User
     * @param SubscriptionPack $plan
     *
     * @return Subscription
     */
    private function createPendingSubscription(User $User, SubscriptionPack $plan): Subscription
    {
        $subscription = $this->subscriptionCreator->create($User, $plan);
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);
        return $subscription;
    }
}