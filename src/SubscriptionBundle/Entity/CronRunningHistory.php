<?php

namespace SubscriptionBundle\Entity;

use CommonDataBundle\Entity\Interfaces\HasUuid;

/**
 * CronRunningHistory entity
 *
 *
 *
 */
class CronRunningHistory implements HasUuid
{
    /**
     * string
     */
    private $uuid;

    /**
     */
    private $lastRunningHour;

    /**
     * CronRunningHistory constructor.
     * @param $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }


    /**
     * @param mixed $uuid
     */
    public function setUuid(string $uuid)
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
     * @param mixed $lastRunningHour
     */
    public function setLastRunningHour($lastRunningHour)
    {
        $this->lastRunningHour = $lastRunningHour;
    }

    /**
     * @return mixed
     */
    public function getLastRunningHour()
    {
        return $this->lastRunningHour;
    }
}