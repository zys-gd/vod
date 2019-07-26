<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 12:02
 */

namespace SubscriptionBundle\Subscription\Renew\DTO;


use SubscriptionBundle\Entity\Subscription;

class MassRenewResult
{
    /**
     * @var int
     */
    private $processed;
    /**
     * @var array|Subscription[]
     */
    private $succeededSubscriptions;
    /**
     * @var int
     */
    private $failedSubscriptions;
    /**
     * @var string|null
     */
    private $error;

    /**
     * MassRenewResult constructor.
     * @param int                  $processed
     * @param array|Subscription[] $succeededSubscriptions
     * @param array|Subscription[] $failedSubscriptions
     * @param string|null          $error
     */
    public function __construct(int $processed, array $succeededSubscriptions, array $failedSubscriptions, string $error = null)
    {
        $this->processed              = $processed;
        $this->succeededSubscriptions = $succeededSubscriptions;
        $this->failedSubscriptions    = $failedSubscriptions;
        $this->error                  = $error;
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