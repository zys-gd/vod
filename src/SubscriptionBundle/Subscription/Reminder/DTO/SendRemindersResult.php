<?php

namespace SubscriptionBundle\Subscription\Reminder\DTO;

use SubscriptionBundle\Entity\Subscription;

/**
 * Class SendRemindersResult
 */
class SendRemindersResult
{
    /**
     * @var int
     */
    private $processed = 0;

    /**
     * @var array|Subscription[]
     */
    private $succeededSubscriptions = [];

    /**
     * @var array|Subscription[]
     */
    private $failedSubscriptions = [];

    /**
     * @var string|null
     */
    private $error = null;

    /**
     * @return int
     */
    public function getProcessed(): int
    {
        return $this->processed;
    }

    /**
     * @return array|Subscription[]
     */
    public function getSucceededSubscriptions(): array
    {
        return $this->succeededSubscriptions;
    }

    /**
     * @param Subscription $subscription
     */
    public function addSuccessSubscription(Subscription $subscription): void
    {
        $this->succeededSubscriptions[] = $subscription;

        $this->processed += 1;
    }

    /**
     * @return array|Subscription[]
     */
    public function getFailedSubscriptions(): array
    {
        return $this->failedSubscriptions;
    }

    /**
     * @param Subscription $subscription
     */
    public function addFailedSubscription(Subscription $subscription): void
    {
        $this->failedSubscriptions[] = $subscription;

        $this->processed += 1;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }
}