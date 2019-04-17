<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\Limiter;
use Symfony\Component\HttpFoundation\Request;
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
     * @param LimiterData|null $limiterData
     *
     * @return bool
     */
    public function isLimitReached(?LimiterData $limiterData): bool
    {
        return $this->limiter->getCarrierProcessingSlots($limiterData) === 0 || $this->limiter->getAffiliateProcessingSlots($limiterData) === 0;
    }

    public function startLimitingProcess(LimiterData $limiterData)
    {
        $this->limiter->decrProcessingSlots($limiterData);
    }

    public function finishLimitingProcess(LimiterData $limiterData)
    {
        $this->limiter->decrSubscriptionSlots($limiterData);
    }

    public function cancelLimitingProcess(LimiterData $limiterData)
    {
        $this->limiter->incrProcessingSlots($limiterData);
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
     * @param SessionInterface $session
     * @param LimiterData      $limiterData
     */
    public function setLimiterData(SessionInterface $session, LimiterData $limiterData): void
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignExtractor->getCampaignFromSession($session);
        if($campaign) {
            /** @var Affiliate $affiliate */
            $affiliate = $campaign->getAffiliate();

            /** @var ConstraintByAffiliate $subscriptionConstraint */
            $subscriptionConstraint = $affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $limiterData->getCarrier());

            $affiliate && $limiterData->setAffiliate($affiliate);
            $subscriptionConstraint && $limiterData->setSubscriptionConstraint($subscriptionConstraint);
        }
    }
}