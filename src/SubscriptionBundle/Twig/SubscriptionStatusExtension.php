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
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SubscriptionStatusExtension extends AbstractExtension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var ArrayCacheService
     */
    private $arrayCacheService;


    /**
     * SubscriptionStatusExtension constructor.
     *
     * @param SessionInterface                                              $session
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor $subscriptionExtractor
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
            new TwigFunction('hasActiveSubscription', [$this, 'hasActiveSubscription']),
            new TwigFunction('hasInActiveSubscription', [$this, 'hasInActiveSubscription']),
            new TwigFunction('isSubscriptionExist', [$this, 'isSubscriptionExist']),
            new TwigFunction('isUnsubscribed', [$this, 'isUnsubscribed']),
            new TwigFunction('hasSubscriptionWithError', [$this, 'hasSubscriptionWithError']),
            new TwigFunction('isNotEnoughCredit', [$this, 'isNotEnoughCredit']),
            new TwigFunction('isNotFullyPaid', [$this, 'isNotFullyPaid']),

            new TwigFunction('isSubscribable', function () {
                return !$this->isSubscriptionExist() || $this->isUnsubscribed() || $this->isNotEnoughCredit();
            }),
            new TwigFunction('isUnsubscribable', function () {

                if ($this->isNotEnoughCredit()) {
                    return false;
                }
                if ($this->isUnsubscribed()) {
                    return false;
                }
                if (!$this->hasActiveSubscription()) {
                    return false;
                }
                return true;

            })
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