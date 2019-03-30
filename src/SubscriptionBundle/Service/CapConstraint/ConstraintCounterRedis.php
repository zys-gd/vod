<?php

namespace SubscriptionBundle\Service\CapConstraint;

use ExtrasBundle\Cache\PureRedisInterface;

/**
 * Class ConstraintCounterRedis
 */
class ConstraintCounterRedis
{
    /**
     * @var PureRedisInterface
     */
    private $redisService;

    /**
     * ConstraintCounterRedis constructor
     *
     * @param PureRedisInterface $redisService
     */
    public function __construct(PureRedisInterface $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * @param string $counterIdentifier
     *
     * @return int|null
     */
    public function getCounter(string $counterIdentifier): ?int
    {
        return $this->redisService->get($this->getCacheKey($counterIdentifier));
    }

    /**
     * @param string $counterIdentifier
     *
     * @return bool
     */
    public function hasCounter(string $counterIdentifier): bool
    {
        return $this->redisService->hasKey($this->getCacheKey($counterIdentifier));
    }

    /**
     * @param string $counterIdentifier
     */
    public function updateCounter(string $counterIdentifier): void
    {
        $cacheKey = $this->getCacheKey($counterIdentifier);
        $counter = $this->redisService->hasKey($cacheKey) ? (int) $this->redisService->get($cacheKey) + 1 : 1;

        $this->redisService->set($cacheKey, $counter);
    }

    /**
     * @param string $counterIdentifier
     */
    public function resetCounter(string $counterIdentifier): void
    {
        $this->redisService->set($this->getCacheKey($counterIdentifier), 0);
    }

    /**
     * @param string $counterIdentifier
     */
    public function removeCounter(string $counterIdentifier): void
    {
        $this->redisService->remove($counterIdentifier);
    }

    /**
     * @param string $counterIdentifier
     *
     * @return string
     */
    private function getCacheKey(string $counterIdentifier): string
    {
        return 'counter_' . $counterIdentifier;
    }
}