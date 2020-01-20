<?php


namespace SubscriptionBundle\Service;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;

class SubscriptionProvider
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var SubscriptionFactory
     */
    private $subscriptionFactory;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        SubscriptionPackProvider $subscriptionPackProvider,
        SubscriptionFactory $subscriptionFactory
    )
    {
        $this->subscriptionRepository   = $subscriptionRepository;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->subscriptionFactory      = $subscriptionFactory;
    }

    /**
     * @param User $user
     *
     * @return Subscription
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     */
    public function obtainSubscription(User $user): Subscription
    {
        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        if (!$subscription) {
            /** @var SubscriptionPack $subscriptionPack */
            $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);
            $subscription     = $this->subscriptionFactory->create($user, $subscriptionPack);
        }

        return $subscription;
    }

}