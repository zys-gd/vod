<?php

namespace App\Domain\ACL\Exception;

use App\Domain\Entity\Campaign;

/**
 * Class CampaignAccessException
 */
class CampaignAccessException extends AccessException
{
    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * CampaignAccessException constructor.
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }
}