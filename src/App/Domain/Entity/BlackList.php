<?php

namespace App\Domain\Entity;

/**
 * BlackList
 */
class BlackList
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $carrierId;

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
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set carrierId
     *
     * @param integer $carrierId
     *
     * @return BlackList
     */
    public function setCarrierId($carrierId)
    {
        $this->carrierId = $carrierId;

        return $this;
    }

    /**
     * Get carrierId
     *
     * @return int
     */
    public function getCarrierId()
    {
        return $this->carrierId;
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
