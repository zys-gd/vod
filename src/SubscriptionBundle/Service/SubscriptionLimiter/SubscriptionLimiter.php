<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;

use IdentificationBundle\Entity\User;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\Limiter;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataMapper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionLimiter implements SubscriptionLimiterInterface
{
    /**
     * @var Limiter
     */
    private $limiter;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var LimiterDataMapper
     */
    private $limiterDataMapper;
    /**
     * @var LimiterDataExtractor
     */
    private $limiterDataExtractor;

    /**
     * SubscriptionLimiter constructor.
     *
     * @param Limiter               $Limiter
     * @param SubscriptionExtractor $subscriptionExtractor
     * @param LimiterDataMapper     $limiterDataMapper
     * @param LimiterDataExtractor  $limiterDataExtractor
     */
    public function __construct(Limiter $Limiter,
        SubscriptionExtractor $subscriptionExtractor,
        LimiterDataMapper $limiterDataMapper,
        LimiterDataExtractor $limiterDataExtractor)
    {
        $this->limiter               = $Limiter;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->limiterDataMapper     = $limiterDataMapper;
        $this->limiterDataExtractor  = $limiterDataExtractor;
    }

    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function isLimitReached(SessionInterface $session): bool
    {
        $carrierLimiterData = $this->limiterDataMapper->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiterDataMapper->createAffiliateLimiterDataFromSession($session);

        if ($affiliateLimiterData && count(array_filter($this->limiterDataExtractor->getAffiliateSlots($affiliateLimiterData))) === 0) {
            return true;
        }
        if ($carrierLimiterData && count(array_filter($this->limiterDataExtractor->getCarrierSlots($carrierLimiterData))) === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param SessionInterface $session
     */
    public function startLimitingProcess(SessionInterface $session)
    {
        $carrierLimiterData = $this->limiterDataMapper->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiterDataMapper->createAffiliateLimiterDataFromSession($session);

        $this->limiter->decrProcessingSlots($carrierLimiterData, $affiliateLimiterData);
    }

    /**
     * @param SessionInterface $session
     */
    public function finishLimitingProcess(SessionInterface $session)
    {
        $carrierLimiterData = $this->limiterDataMapper->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiterDataMapper->createAffiliateLimiterDataFromSession($session);

        $this->limiter->decrSubscriptionSlots($carrierLimiterData, $affiliateLimiterData);
    }

    /**
     * @param SessionInterface $session
     */
    public function cancelLimitingProcess(SessionInterface $session)
    {
        $carrierLimiterData = $this->limiterDataMapper->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiterDataMapper->createAffiliateLimiterDataFromSession($session);

        $this->limiter->incrProcessingSlots($carrierLimiterData, $affiliateLimiterData);
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function need2BeLimited(User $user): bool
    {
        return !(bool)$this->subscriptionExtractor->getExistingSubscriptionForUser($user);
    }
}