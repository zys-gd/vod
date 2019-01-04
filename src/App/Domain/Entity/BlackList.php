<?php

namespace App\Domain\Entity;

/**
 * BlackList
 */
class BlackList
{
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

    /**
     * BlackList constructor.
     */
    public function __construct()
    {
        $this->addedAt = new \DateTime();
        $this->isBlockedManually = true;
        // $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * Get id
     *
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
     *
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
     *
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
}
