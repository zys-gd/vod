<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.01.19
 * Time: 14:19
 */

namespace SubscriptionBundle\Twig;


use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;
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
     * @var ArrayCacheService
     */
    private $arrayCacheService;


    /**
     * SubscriptionStatusExtension constructor.
     *
     * @param SessionInterface      $session
     * @param SubscriptionExtractor $subscriptionExtractor
     */
    public function __construct(SessionInterface $session, SubscriptionExtractor $subscriptionExtractor, ArrayCacheService $arrayCacheService)
    {
        $this->session               = $session;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->arrayCacheService     = $arrayCacheService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('hasActiveSubscription', [$this, 'hasActiveSubscription']),
            new \Twig_SimpleFunction('hasInActiveSubscription', [$this, 'hasInActiveSubscription']),
            new \Twig_SimpleFunction('isSubscriptionExist', [$this, 'isSubscriptionExist']),
            new \Twig_SimpleFunction('isUnsubscribed', [$this, 'isUnsubscribed']),
            new \Twig_SimpleFunction('hasSubscriptionWithError', [$this, 'hasSubscriptionWithError']),
            new \Twig_SimpleFunction('isNotEnoughCredit', [$this, 'isNotEnoughCredit']),
            new \Twig_SimpleFunction('isNotFullyPaid', [$this, 'isNotFullyPaid']),
        ];
    }

    /**
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {

        if ($subscription = $this->getPreparedSubscription()) {
            return $subscription->getStatus() == Subscription::IS_ACTIVE && $subscription->getCurrentStage() == Subscription::ACTION_SUBSCRIBE;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasInActiveSubscription(): bool
    {
        if ($subscription = $this->getPreparedSubscription()) {
            return $subscription->getStatus() == Subscription::IS_INACTIVE;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSubscriptionExist(): bool
    {
        if ($subscription = $this->getPreparedSubscription()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isUnsubscribed(): bool
    {
        if ($subscription = $this->getPreparedSubscription()) {
            return $subscription->getStatus() == Subscription::IS_INACTIVE && $subscription->getCurrentStage() == Subscription::ACTION_UNSUBSCRIBE;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasSubscriptionWithError()
    {
        if ($subscription = $this->getPreparedSubscription()) {
            return $subscription->hasError();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isNotEnoughCredit(): bool
    {
        $subscription = $this->getPreparedSubscription();
        if ($subscription && $subscription->isNotEnoughCredit()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isNotFullyPaid(): bool
    {
        $subscription = $this->getPreparedSubscription();
        if ($subscription && $subscription->isNotFullyPaid()) {
            return true;
        }
        return false;
    }

    private function getPreparedSubscription(): ?Subscription
    {
        $key = sprintf('%s_%s', $this->session->getId(), 'subscription');

        if ($this->arrayCacheService->hasCache($key)) {
            $subscription = $this->arrayCacheService->getValue($key);
        } else {
            $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($this->session);
            $this->arrayCacheService->saveCache($key, $subscription, 0);
        }
        return $subscription;

    }
}