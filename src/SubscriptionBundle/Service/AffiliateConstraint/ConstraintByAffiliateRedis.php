<?php

namespace SubscriptionBundle\Service\AffiliateConstraint;

use ExtrasBundle\Cache\PureRedisInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class ConstraintByAffiliateRedis
 */
class ConstraintByAffiliateRedis
{
    /**
     * @var PureRedisInterface
     */
    private $redisService;

    /**
     * ConstraintByAffiliateRedis constructor
     *
     * @param PureRedisInterface $redisService
     */
    public function __construct(PureRedisInterface $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @return int|null
     */
    public function getCounter(ConstraintByAffiliate $constraintByAffiliate): ?int
    {
        return $this->redisService->get($this->getCacheKey($constraintByAffiliate));
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @return bool
     */
    public function hasCounter(ConstraintByAffiliate $constraintByAffiliate): bool
    {
        return $this->redisService->hasKey($this->getCacheKey($constraintByAffiliate));
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     */
    public function updateCounter(ConstraintByAffiliate $constraintByAffiliate): void
    {
        $cacheKey = $this->getCacheKey($constraintByAffiliate);
        $counter = $this->redisService->hasKey($cacheKey) ? (int) $this->redisService->get($cacheKey) + 1 : 1;

        $this->redisService->set($cacheKey, $counter);
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     */
    public function resetCounter(ConstraintByAffiliate $constraintByAffiliate)
    {
        $this->redisService->set($this->getCacheKey($constraintByAffiliate), 0);
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