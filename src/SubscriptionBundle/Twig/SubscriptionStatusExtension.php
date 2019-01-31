<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.01.19
 * Time: 14:19
 */

namespace SubscriptionBundle\Twig;


use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionStatusExtension extends \Twig_Extension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;


    /**
     * SubscriptionStatusExtension constructor.
     * @param SessionInterface       $session
     * @param UserRepository         $repository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(SessionInterface $session, UserRepository $repository, SubscriptionRepository $subscriptionRepository)
    {
        $this->session                = $session;
        $this->repository             = $repository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('hasActiveSubscription', [$this, 'hasActiveSubscription']),
            new \Twig_SimpleFunction('hasInActiveSubscription', [$this, 'hasInActiveSubscription']),
            new \Twig_SimpleFunction('isSubscriptionExist', [$this, 'isSubscriptionExist']),
            new \Twig_SimpleFunction('isUnsubscribed', [$this, 'isUnsubscribed']),
        ];
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasActiveSubscription(): bool
    {
        if($subscription = $this->extractSubscription()) {
            return $subscription->getStatus() == Subscription::IS_ACTIVE && $subscription->getCurrentStage() == Subscription::ACTION_SUBSCRIBE;
        }
        return false;
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasInActiveSubscription(): bool
    {
        if($subscription = $this->extractSubscription()) {
            return $subscription->getStatus() == Subscription::IS_INACTIVE;
        }
        return false;
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isSubscriptionExist(): bool
    {
        if($subscription = $this->extractSubscription()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isUnsubscribed(): bool
    {
        if($subscription = $this->extractSubscription()) {
            return $subscription->getStatus() == Subscription::IS_INACTIVE && $subscription->getCurrentStage() == Subscription::ACTION_UNSUBSCRIBE;
        }
        return false;
    }

    /**
     * @return Subscription|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function extractSubscription(): ?Subscription
    {
        $extractIdentificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
        if (isset($extractIdentificationData['identification_token'])) {
            $user = $this->repository->findOneByIdentificationToken($extractIdentificationData['identification_token']);
            if ($user) {
                $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);

                return $subscription;
            }
        }
        return null;
    }
}