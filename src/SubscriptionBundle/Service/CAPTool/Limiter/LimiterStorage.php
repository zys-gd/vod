<?php


namespace SubscriptionBundle\Service\CAPTool\Limiter;


use Redis;

class LimiterStorage
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * LimiterStorage constructor.
     *
     * @param                      $redis
     */
    public function __construct(
        \Redis $redis
    )
    {
        $this->redis = $redis;
    }

    public function resetPendingCounter(string $key): void
    {
        $keys = $this->redis->keys(sprintf('pending_%s*', $key));
        $this->redis->delete($keys);
    }

    public function resetFinishedCounter(string $key): void
    {
        $keys = $this->redis->keys(sprintf('finished_%s*', $key));
        $this->redis->delete($keys);
    }

    public function storePendingSubscription(string $key, string $sessionId): void
    {
        $this->redis->set(sprintf('pending_%s_%s', $key, $sessionId), $sessionId, 60);

        $this->redis->set(sprintf('session_%s', $sessionId), $key, 60);
    }


    public function getPendingSubscriptionAmount(string $key): int
    {
        return count($this->redis->keys(sprintf('pending_%s*', $key)));
    }

    public function isSubscriptionAlreadyPending(string $sessionId): bool
    {
        $value = $this->redis->get(sprintf('session_%s', $sessionId));

        return !($value === false);
    }


    public function storeFinishedSubscription(string $key, string $uuid): void
    {
        /*$timeout = $this->calculateSecondsUntilTomorrow();*/
        $timeout = 10;

        $redisKey = sprintf('finished_%s[%s]', $key, $uuid);
        $this->redis->set($redisKey, json_encode([
            'uuid' => $uuid
        ]), $timeout);

        $this->redis->persist($redisKey);
    }


    public function getFinishedSubscriptionAmount(string $key): int
    {
        return count($this->redis->keys(sprintf('finished_%s*', $key)));
    }

    public function removePendingSubscription()
    {
        // Do nothing and win ?
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