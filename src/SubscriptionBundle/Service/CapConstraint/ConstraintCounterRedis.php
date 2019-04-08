<?php

namespace SubscriptionBundle\Service\CapConstraint;

use Psr\Log\LoggerInterface;

/**
 * Class ConstraintCounterRedis
 */
class ConstraintCounterRedis
{
    /**
     * @var \Predis\Client|\Redis|\RedisCluster
     */
    private $redisService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ConstraintCounterRedis constructor
     *
     * @param \Predis\Client|\Redis|\RedisCluster $redisService
     */
    public function __construct($redisService, LoggerInterface $logger)
    {
        $this->redisService = $redisService;
        $this->logger = $logger;
    }

    /**
     * @param string $counterIdentifier
     *
     * @return int|null
     */
    public function getCounter(string $counterIdentifier): ?int
    {
        $this->logger->info('Counter key', [
            $this->getCacheKey($counterIdentifier)
        ]);

        $this->logger->info('Counter value', [
            $this->redisService->get($this->getCacheKey($counterIdentifier))
        ]);

        return $this->redisService->get($this->getCacheKey($counterIdentifier));
    }

    /**
     * @param string $counterIdentifier
     *
     * @return bool
     */
    public function hasCounter(string $counterIdentifier): bool
    {
        return $this->redisService->exists($this->getCacheKey($counterIdentifier));
    }

    /**
     * @param string $counterIdentifier
     */
    public function updateCounter(string $counterIdentifier): void
    {
        $cacheKey = $this->getCacheKey($counterIdentifier);
        $counter = $this->redisService->exists($cacheKey) ? (int) $this->redisService->get($cacheKey) + 1 : 1;

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
        $this->redisService->del([$counterIdentifier]);
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