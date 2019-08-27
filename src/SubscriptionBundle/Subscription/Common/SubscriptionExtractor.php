<?php

namespace SubscriptionBundle\Subscription\Common;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionExtractor
{
    private $subscriptionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * SubscriptionProvider constructor.
     *
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserRepository         $userRepository
     */
    public function __construct(SubscriptionRepository $subscriptionRepository, UserRepository $userRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository         = $userRepository;
    }

    /**
     * @param CarrierInterface $carrier
     * @return Subscription[]
     * @throws \Exception
     */
    public function getTrialSubscriptionsToRenew(CarrierInterface $carrier)
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

    /**
     * @param Session $session
     *
     * @return Subscription|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function extractSubscriptionFromSession(SessionInterface $session): ?Subscription
    {
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($session);
        if ($identificationToken) {
            $user = $this->userRepository->findOneByIdentificationToken($identificationToken);
            if ($user) {
                $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);

                return $subscription;
            }
        }
        return null;
    }
}