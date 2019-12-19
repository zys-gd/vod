<?php

namespace SubscriptionBundle\Subscription\Reminder\DTO;

/**
 * Class RemindSettings
 */
class RemindSettings
{
    /**
     * @var int
     */
    private $daysInterval;

    /**
     * @var string
     */
    private $body;

    /**
     * RemindSettings constructor.
     *
     * @param int    $daysInterval
     * @param string $body
     */
    public function __construct(int $daysInterval, string $body)
    {
        $this->daysInterval = $daysInterval;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getDaysInterval(): int
    {
        return $this->daysInterval;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}