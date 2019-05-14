<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 14:08
 */

namespace SubscriptionBundle\Affiliate\CapConstraint;


class VisitStorage
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * VisitStorage constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function cleanVisits()
    {
        $keys = $this->redis->keys('visit-*');

        foreach ($keys as $key) {
            $this->redis->del($key);
        }
    }

    /**
     * @param string $key
     * @param string $visitInfo
     */
    public function storeVisit(string $key, string $visitInfo): void
    {
        $this->redis->lPush(
            $key,
            $visitInfo
        );
    }

    public function getVisitCount(string $key): int
    {
        $values = $this->redis->lRange($key, 0, -1);
        return $values ? count($values) : 0;
    }
}