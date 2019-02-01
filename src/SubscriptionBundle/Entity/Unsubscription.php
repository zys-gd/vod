<?php

namespace SubscriptionBundle\Entity;

/**
 * Class Unsubscription
 */
class Unsubscription
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'failed';

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var \DateTime
     */
    private $unsubscribeDate;

    /**
     * @var string
     */
    private $status;

    /**
     * Unsubscription constructor
     *
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->unsubscribeDate = new \DateTime('now');
    }

    /**
     * @param string $type
     *
     * @return Unsubscription
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param Subscription $subscription
     *
     * @return Unsubscription
     */
    public function setSubscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @param string $status
     *
     * @return Unsubscription
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param \DateTime $unsubscribeDate
     *
     * @return Unsubscription
     */
    public function setUnsubscribeDate(\DateTime $unsubscribeDate): self
    {
        $this->unsubscribeDate = $unsubscribeDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUnsubscribeDate(): \DateTime
    {
        return $this->unsubscribeDate;
    }
}