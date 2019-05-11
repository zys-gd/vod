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

        foreach ($keys as $key) {
            $this->redis->del($key);
        }
    }

    public function resetFinishedCounter(string $key): void
    {
        $keys = $this->redis->keys(sprintf('finished_%s*', $key));

        foreach ($keys as $key) {
            $this->redis->del($key);
        }
    }

    public function storePendingSubscription(string $key, string $sessionId): void
    {
        $this->redis->set(sprintf('pending_%s_%s', $key, $sessionId), $sessionId, 180);

        $this->redis->set(sprintf('session_%s', $sessionId), $key, 180);
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
        $this->redis->set(sprintf('finished_%s[%s]', $key, $uuid), json_encode([
            'uuid' => $uuid
        ]));
    }


    public function getFinishedSubscriptionAmount(string $key): int
    {
        return count($this->redis->keys(sprintf('finished_%s*', $key)));
    }

    public function removePendingSubscription()
    {
        // Do nothing and win ?
    }

}