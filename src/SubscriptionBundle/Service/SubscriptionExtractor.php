<?php

namespace SubscriptionBundle\Service;


use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;

class SubscriptionExtractor
{
    private $subscriptionRepository;

    /**
     * SubscriptionProvider constructor.
     * @param $subscriptionRepository
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param Carrier $carrier
     * @return Subscription[]
     */
    public function getTrialSubscriptionsToRenew(Carrier $carrier)
    {
        $subscriptions = $this->subscriptionRepository->getExpiredSubscriptions($carrier);


        return $subscriptions;
    }


    /**
     * @param User $user
     * @return Subscription
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExistingSubscriptionForUser(User $user): ?Subscription
    {
        return $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);
    }


    public function getExistingSubscriptionForMsisdn($msisdn)
    {

    }

}