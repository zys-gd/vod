<?php

namespace SubscriptionBundle\Entity;

use IdentificationBundle\Entity\User;

/**
 * Class Refund
 */
class Refund
{
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    const STATUS_FAILED = 'failed';

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var integer
     */
    protected $bfChargeProcessId;

    /**
     * @var integer
     */
    protected $bfRefundProcessId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $error = null;

    /**
     * @var \DateTime
     */
    protected $attemptDate;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var integer
     */
    protected $refundValue;

    /**
     * Refund constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getBfChargeProcessId(): int
    {
        return $this->bfChargeProcessId;
    }

    /**
     * @param int $bfChargeProcessId
     *
     * @return Refund
     */
    public function setBfChargeProcessId(int $bfChargeProcessId): self
    {
        $this->bfChargeProcessId = $bfChargeProcessId;

        return $this;
    }

    /**
     * @return int
     */
    public function getBfRefundProcessId(): int
    {
        return $this->bfRefundProcessId;
    }

    /**
     * @param int|null $bfRefundProcessId
     *
     * @return Refund
     */
    public function setBfRefundProcessId(?int $bfRefundProcessId): self
    {
        $this->bfRefundProcessId = $bfRefundProcessId;

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
     * @param string $status
     *
     * @return Refund
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     *
     * @return Refund
     */
    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAttemptDate(): \DateTime
    {
        return $this->attemptDate;
    }

    /**
     * @param \DateTime $attemptDate
     *
     * @return Refund
     */
    public function setAttemptDate(\DateTime $attemptDate): self
    {
        $this->attemptDate = $attemptDate;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Refund
     */
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRefundValue(): ?int
    {
        return $this->refundValue;
    }

    /**
     * @param int|null $refundValue
     *
     * @return Refund
     */
    public function setRefundValue(?int $refundValue): self
    {
        $this->refundValue = $refundValue;

        return $this;
    }
}