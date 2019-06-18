<?php


namespace App\Domain\Entity;


use DateTime;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;


class CampaignSchedule implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $dayStart = 1;

    /**
     * @var int
     */
    private $dayEnd = 1;

    /**
     * @var CampaignInterface
     */
    private $campaign;

    /**
     * @var DateTime
     */
    private $timeStart;
    /**
     * @var DateTime
     */
    private $timeEnd;
    /**
     * CampaignSchedule constructor.
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
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return int
     */
    public function getDayStart(): int
    {
        return $this->dayStart;
    }

    /**
     * @param int $dayStart
     */
    public function setDayStart(int $dayStart): void
    {
        $this->dayStart = $dayStart;
    }

    /**
     * @return int
     */
    public function getDayEnd(): int
    {
        return $this->dayEnd;
    }

    /**
     * @param int $dayEnd
     */
    public function setDayEnd(int $dayEnd): void
    {
        $this->dayEnd = $dayEnd;
    }

    /**
     * @return CampaignInterface
     */
    public function getCampaign(): CampaignInterface
    {
        return $this->campaign;
    }

    /**
     * @param CampaignInterface $campaign
     */
    public function setCampaign(CampaignInterface $campaign): void
    {
        $this->campaign = $campaign;
    }

    /**
     * @return DateTime
     */
    public function getTimeStart(): ?DateTime
    {
        return $this->timeStart;
    }

    /**
     * @param DateTime  $timeStart
     */
    public function setTimeStart(DateTime  $timeStart): void
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return DateTime
     */
    public function getTimeEnd(): ?DateTime
    {
        return $this->timeEnd;
    }

    /**
     * @param DateTime  $timeEnd
     */
    public function setTimeEnd(DateTime  $timeEnd): void
    {
        $this->timeEnd = $timeEnd;
    }

}