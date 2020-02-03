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
    private $succeededSubscriptions;

    /**
     * @var array|Subscription[]
     */
    private $failedSubscriptions;

    /**
     * @var string|null
     */
    private $error = null;

    /**
     * SendRemindersResult constructor
     *
     * @param array       $succeededSubscriptions
     * @param array       $failedSubscriptions
     * @param string|null $error
     */
    public function __construct(
        array $succeededSubscriptions = [],
        array $failedSubscriptions = [],
        string $error = null
    ) {
        $this->succeededSubscriptions = $succeededSubscriptions;
        $this->failedSubscriptions = $failedSubscriptions;
        $this->error = $error;
    }

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
     * @return array|Subscription[]
     */
    public function getFailedSubscriptions(): array
    {
        return $this->failedSubscriptions;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}