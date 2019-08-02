<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 14:08
 */

namespace SubscriptionBundle\CAPTool\Visit;


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

    public function cleanVisits(string $key = 'visit-*'): void
    {
        $keys = $this->redis->keys($key);
        $this->redis->del($keys);
    }

    /**
     * @param string $key
     * @param string $visitInfo
     */
    public function storeVisit(string $key, string $visitInfo): void
    {
        //$timeout = $this->calculateSecondsUntilTomorrow();
        $timeout = 10;

        $redisKey = sprintf('%s-%s', $key, $visitInfo);
        $this->redis->set($redisKey, $visitInfo, $timeout);
        $this->redis->persist($redisKey);
    }

    public function getVisitCount(string $key): int
    {
        $keys = $this->redis->keys(sprintf("%s-*", $key));

        return count($keys);
    }


    private function calculateSecondsUntilTomorrow(): int
    {
        $midnight = strtotime("tomorrow 00:00:00");

        if ($midnight === false) {
            return 0;
        }

        $timeTo = $midnight - time();
        return $timeTo;
    }
}