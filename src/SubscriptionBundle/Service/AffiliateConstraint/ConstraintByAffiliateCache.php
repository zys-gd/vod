<?php

namespace SubscriptionBundle\Service\AffiliateConstraint;

use ExtrasBundle\Cache\ICacheService;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class ConstraintByAffiliateCache
 */
class ConstraintByAffiliateCache
{
    /**
     * @var ICacheService
     */
    private $cache;

    /**
     * ConstraintByAffiliateCache constructor
     *
     * @param ICacheService $cacheService
     */
    public function __construct(ICacheService $cacheService)
    {
        $this->cache = $cacheService;
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @return int|null
     */
    public function getCounter(ConstraintByAffiliate $constraintByAffiliate): ?int
    {
        return $this->cache->getValue($this->getCacheKey($constraintByAffiliate));
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @return bool
     */
    public function hasCounter(ConstraintByAffiliate $constraintByAffiliate): bool
    {
        return $this->cache->hasCache($this->getCacheKey($constraintByAffiliate));
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     */
    public function updateCounter(ConstraintByAffiliate $constraintByAffiliate): void
    {
        $cacheKey = $this->getCacheKey($constraintByAffiliate);
        $counter = $this->cache->hasCache($cacheKey) ? $this->cache->getValue($cacheKey) + 1 : 1;

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