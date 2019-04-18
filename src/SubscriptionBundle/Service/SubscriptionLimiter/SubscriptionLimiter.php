<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\Limiter;
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
     * SubscriptionLimiter constructor.
     *
     * @param Limiter               $Limiter
     * @param SubscriptionExtractor $subscriptionExtractor
     */
    public function __construct(Limiter $Limiter, SubscriptionExtractor $subscriptionExtractor)
    {
        $this->limiter               = $Limiter;
        $this->subscriptionExtractor = $subscriptionExtractor;
    }

    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function isLimitReached(SessionInterface $session): bool
    {
        $carrierLimiterData = $this->limiter->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiter->createAffiliateLimiterDataFromSession($session);

        if ($carrierLimiterData && $this->limiter->getCarrierProcessingSlots($carrierLimiterData) === 0) {
            return true;
        }
        if ($affiliateLimiterData && $this->limiter->getAffiliateProcessingSlots($affiliateLimiterData) === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param SessionInterface $session
     */
    public function startLimitingProcess(SessionInterface $session)
    {
        $carrierLimiterData = $this->limiter->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiter->createAffiliateLimiterDataFromSession($session);

        $this->limiter->decrProcessingSlots($carrierLimiterData, $affiliateLimiterData);
    }

    /**
     * @param SessionInterface $session
     */
    public function finishLimitingProcess(SessionInterface $session)
    {
        $carrierLimiterData = $this->limiter->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiter->createAffiliateLimiterDataFromSession($session);

        $this->limiter->decrSubscriptionSlots($carrierLimiterData, $affiliateLimiterData);
    }

    /**
     * @param SessionInterface $session
     */
    public function cancelLimitingProcess(SessionInterface $session)
    {
        $carrierLimiterData = $this->limiter->createCarrierLimiterDataFromSession($session);

        $affiliateLimiterData = $this->limiter->createAffiliateLimiterDataFromSession($session);

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