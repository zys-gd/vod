<?php

namespace SubscriptionBundle\Service;

use ExtrasBundle\Cache\ICacheService;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ConstraintByAffiliateService
 */
class ConstraintByAffiliateService
{
    /**
     * @var ICacheService
     */
    private $cache;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * ConstraintByAffiliateService constructor
     *
     * @param ICacheService $cacheService
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(ICacheService $cacheService, CampaignRepositoryInterface $campaignRepository)
    {
        $this->cache = $cacheService;
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @return RedirectResponse|null
     */
    public function handleLandingPageRequest(CampaignInterface $campaign)
    {
        $affiliate = $campaign->getAffiliate();
        $constraints = $affiliate->getConstraints();

        foreach ($constraints as $constraint) {
            $cacheKey = $this->getCacheKey($constraint);

            if (!$this->cache->hasCache($cacheKey)) {
                continue;
            }

            $isLimitReached = $this->cache->getValue($cacheKey) >= $constraint->getNumberOfActions();

            if ($isLimitReached) {
                return new RedirectResponse($constraint->getRedirectUrl());
            }
        }

        return null;
    }

    /**
     * @param Subscription $subscription
     */
    public function updateSubscribeCounter(Subscription $subscription): void
    {
        $affiliateToken = $subscription->getAffiliateToken();

        if (!$affiliateToken || empty($affiliateToken['cid'])) {
            return;
        }

        /** @var CampaignInterface $campaign */
        $campaign = $this->campaignRepository->findOneByCampaignToken($affiliateToken['cid']);
        $affiliate = $campaign->getAffiliate();

        $this->updateCounter($affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE));
    }

    /**
     * @param AffiliateInterface $affiliate
     */
    public function updateVisitCounter(AffiliateInterface $affiliate): void
    {
        $this->updateCounter($affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_VISIT));
    }

    /**
     * @param ConstraintByAffiliate|null $constraintByAffiliate
     */
    private function updateCounter(?ConstraintByAffiliate $constraintByAffiliate): void
    {
        if (!$constraintByAffiliate) {
            return;
        }

        $cacheKey = $this->getCacheKey($constraintByAffiliate);

        if ($this->cache->hasCache($cacheKey)) {
            $counter = $this->cache->getValue($cacheKey) + 1;
        } else {
            $counter = 1;
        }

        $this->cache->saveCache($cacheKey, $counter, 86400);
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @return string
     */
    private function getCacheKey(ConstraintByAffiliate $constraintByAffiliate): string
    {
        return 'counter_' . $constraintByAffiliate->getUuid();
    }
}