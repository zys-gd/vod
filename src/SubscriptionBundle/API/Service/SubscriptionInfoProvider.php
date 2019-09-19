<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.09.19
 * Time: 15:10
 */

namespace SubscriptionBundle\API\Service;


use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\API\DTO\SubscriptionInfo;
use SubscriptionBundle\API\Exception\NotFoundException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;

class SubscriptionInfoProvider
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;


    /**
     * SubscriptionInfoProvider constructor.
     * @param UserRepository         $userRepository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(UserRepository $userRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->userRepository         = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function getSubscription(string $identifier): Subscription
    {
        $user = $this->userRepository->findOneByMsisdn($identifier);

        if (!$user) {
            throw new NotFoundException(sprintf('User with msisdn `%s` not found', $identifier), $identifier);
        }

        $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);

        if (!$subscription) {
            throw new NotFoundException(sprintf('Subscription for user with msisdn `%s` not found', $identifier), $identifier);
        }

        return $subscription;

    }
}