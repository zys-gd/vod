<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.01.19
 * Time: 14:19
 */

namespace SubscriptionBundle\Twig;


use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionStatusExtension extends \Twig_Extension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;


    /**
     * SubscriptionStatusExtension constructor.
     *
     * @param SessionInterface      $session
     * @param SubscriptionExtractor $subscriptionExtractor
     */
    public function __construct(SessionInterface $session, SubscriptionExtractor $subscriptionExtractor)
    {
        $this->session = $session;
        $this->subscriptionExtractor = $subscriptionExtractor;
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
        if ($subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($this->session)) {
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
        if ($subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($this->session)) {
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
        if ($subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($this->session)) {
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
        if ($subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($this->session)) {
            return $subscription->getStatus() == Subscription::IS_INACTIVE && $subscription->getCurrentStage() == Subscription::ACTION_UNSUBSCRIBE;
        }
        return false;
    }
}