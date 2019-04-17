<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\User;
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
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var Limiter
     */
    private $limiter;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;

    public function __construct(CampaignExtractor $campaignExtractor,
        SubscriptionExtractor $subscriptionExtractor,
        Limiter $Limiter)
    {
        $this->campaignExtractor     = $campaignExtractor;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->limiter               = $Limiter;
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     *
     * @return bool
     */
    public function isLimitReached(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData): bool
    {
        if ($carrierLimiterData && $this->limiter->getCarrierProcessingSlots($carrierLimiterData) === 0) {
            return true;
        }
        if ($affiliateLimiterData && $this->limiter->getAffiliateProcessingSlots($affiliateLimiterData) === 0) {
            return true;
        }
        return false;
    }

    public function startLimitingProcess(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
        $this->limiter->decrProcessingSlots($carrierLimiterData, $affiliateLimiterData);
    }

    public function finishLimitingProcess(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
        $this->limiter->decrSubscriptionSlots($carrierLimiterData, $affiliateLimiterData);
    }

    public function cancelLimitingProcess(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
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

    /**
     * @param SessionInterface          $session
     * @param CarrierLimiterData        $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function setLimiterData(SessionInterface $session,
        ?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData): void
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignExtractor->getCampaignFromSession($session);
        if ($campaign) {
            /** @var Affiliate $affiliate */
            $affiliate = $campaign->getAffiliate();

            /** @var ConstraintByAffiliate $subscriptionConstraint */
            $subscriptionConstraint = $affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $carrierLimiterData->getCarrier());

            $affiliate && $carrierLimiterData->setAffiliate($affiliate);
            $subscriptionConstraint && $carrierLimiterData->setSubscriptionConstraint($subscriptionConstraint);
        }
    }
}