<?php

namespace SubscriptionBundle\Entity;

/**
 * Class SubscriptionReminder
 */
class SubscriptionReminder
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var \DateTime
     */
    private $lastReminderSent;

    /**
     * SubscriptionReminder constructor
     *
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->lastReminderSent = new \DateTime();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return SubscriptionReminder
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     *
     * @return SubscriptionReminder
     */
    public function setSubscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastReminderSent(): ?\DateTime
    {
        return $this->lastReminderSent;
    }

    /**
     * @param \DateTime $lastReminderSent
     *
     * @return SubscriptionReminder
     */
    public function setLastReminderSent(\DateTime $lastReminderSent): self
    {
        $this->lastReminderSent = $lastReminderSent;

        return $this;
    }
}