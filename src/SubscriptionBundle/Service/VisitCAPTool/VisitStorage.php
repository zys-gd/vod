<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 14:08
 */

namespace SubscriptionBundle\Service\VisitCAPTool;


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

    public function cleanVisits(string $key = 'visit-*')
    {
        $keys = $this->redis->keys($key);

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
        $timeout = $this->calculateSecondsUntilTomorrow();

        $this->redis->set(sprintf('%s-%s', $key, $visitInfo), $visitInfo, $timeout);
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