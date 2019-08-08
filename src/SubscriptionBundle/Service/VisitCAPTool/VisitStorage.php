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

    public function cleanVisits(string $key = 'visit-*'): void
    {

    }

    /**
     * @param string                  $key
     * @param string                  $visitInfo
     * @param \DateTimeInterface|null $dateTime
     */
    public function storeVisit(string $key, string $visitInfo, \DateTimeInterface $dateTime = null): void
    {
        if ($dateTime) {
            $date = $dateTime->format('Ymd');
        } else {
            $date = date('Ymd');
        }

        $this->redis->pfAdd(sprintf('%s-%s', $key, $date), [$visitInfo]);
    }

    public function getVisitCount(string $key, \DateTimeInterface $dateTime = null): int
    {
        if ($dateTime) {
            $date = $dateTime->format('Ymd');
        } else {
            $date = date('Ymd');
        }

        $count = $this->redis->pfCount(sprintf('%s-%s', $key, $date));

        return $count;
    }

}