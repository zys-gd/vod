<?php

namespace SubscriptionBundle\Entity;

/**
 * BlackList
 */
class BlackList
{
    const PERIODICITY_TYPE = [
        'permanently' => 0,
        'range'       => 1
    ];

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $billingCarrierId;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var bool
     */
    private $isBlockedManually;

    /**
     * @var \DateTime
     */
    private $addedAt;

    /** @var int */
    private $duration = 0;

    /** @var \DateTime */
    private $banStart;

    /** @var \DateTime */
    private $banEnd;

    /**
     * BlackList constructor.
     *
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid              = $uuid;
        $this->addedAt           = new \DateTime();
        $this->isBlockedManually = true;
    }

    /**
     * Get id
     * @return int
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set carrierId
     *
     * @param integer $billingCarrierId
     *
     * @return BlackList
     */
    public function setBillingCarrierId($billingCarrierId)
    {
        $this->billingCarrierId = $billingCarrierId;

        return $this;
    }

    /**
     * Get carrierId
     * @return int
     */
    public function getBillingCarrierId()
    {
        return $this->billingCarrierId;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return BlackList
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isBlockedManually(): bool
    {
        return $this->isBlockedManually;
    }

    /**
     * @param bool $isBlockedManually
     */
    public function setIsBlockedManually(bool $isBlockedManually)
    {
        $this->isBlockedManually = $isBlockedManually;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt(): \DateTime
    {
        return $this->addedAt;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return \DateTime
     */
    public function getBanStart(): ?\DateTime
    {
        return $this->banStart;
    }

    /**
     * @param \DateTime $banStart
     */
    public function setBanStart(\DateTime $banStart): void
    {
        $this->banStart = $banStart;
    }

    /**
     * @return \DateTime
     */
    public function getBanEnd(): ?\DateTime
    {
        return $this->banEnd;
    }

    /**
     * @param \DateTime $banEnd
     */
    public function setBanEnd(\DateTime $banEnd): void
    {
        $this->banEnd = $banEnd;
    }
}
