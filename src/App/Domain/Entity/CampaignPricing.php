<?php

namespace App\Domain\Entity;

/**
 * CampaignPricing
 */
class CampaignPricing
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * @var string
     */
    private $ppd;

    /**
     * @var string
     */
    private $sub;

    /**
     * @var string
     */
    private $click;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    private $active = true;

    /**
     * CampaignPricing constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active=$active;
        return $this;
    }

    public function disable()
    {
        $this->setActive(false);
    }

    public function enable()
    {
        $this->setActive(true);
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
     * Set campaign
     *
     * @param Campaign $campaign
     *
     * @return CampaignPricing
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Set ppd
     *
     * @param string $ppd
     *
     * @return CampaignPricing
     */
    public function setPpd($ppd)
    {
        $this->ppd = $ppd;

        return $this;
    }

    /**
     * Get ppd
     *
     * @return string
     */
    public function getPpd()
    {
        return $this->ppd;
    }

    /**
     * Set sub
     *
     * @param string $sub
     *
     * @return CampaignPricing
     */
    public function setSub($sub)
    {
        $this->sub = $sub;

        return $this;
    }

    /**
     * Get sub
     *
     * @return string
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * Set click
     *
     * @param string $click
     *
     * @return CampaignPricing
     */
    public function setClick($click)
    {
        $this->click = $click;

        return $this;
    }

    /**
     * Get click
     *
     * @return string
     */
    public function getClick()
    {
        return $this->click;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return CampaignPricing
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return CampaignPricing
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

}

