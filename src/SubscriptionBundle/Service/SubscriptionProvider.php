<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 14:20
 */

namespace SubscriptionBundle\Service;


use AppBundle\Entity\Carrier;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use UserBundle\Entity\BillableUser;

class SubscriptionProvider
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
     * @param BillableUser $user
     * @return Subscription
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExistingSubscriptionForBillableUser(BillableUser $user)
    {
        return $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);
    }


    public function getExistingSubscriptionForMsisdn($msisdn)
    {

    }

}