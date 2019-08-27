<?php

namespace SubscriptionBundle\Entity;

use CommonDataBundle\Entity\Interfaces\HasUuid;

class CronTask implements HasUuid
{
    /** @var string */
    private $uuid;

    /** @var  */
    private $cronName;

    /** @var  */
    private $isRunning;

    /**
     * CronTask constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get cronName
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getCronName();
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
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getCronName(): string
    {
        return $this->cronName;
    }

    /**
     * @param string $cronName
     */
    public function setCronName(string $cronName)
    {
        $this->cronName = $cronName;
    }

    /**
     * @return int
     */
    public function getisRunning(): int
    {
        return $this->isRunning;
    }


    /**
     * @param int $isRunning
     */
    public function setIsRunning(int $isRunning)
    {
        $this->isRunning = $isRunning;
    }

}
