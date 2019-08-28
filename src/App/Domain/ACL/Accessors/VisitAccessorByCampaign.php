<?php

namespace App\Domain\ACL\Accessors;

use App\Domain\Entity\Campaign;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;

/**
 * Class VisitAccessorByCampaign
 */
class VisitAccessorByCampaign
{
    /**
     * @param Campaign $campaign
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canVisit(Campaign $campaign, CarrierInterface $carrier): bool
    {
        if ($carrier->getIsCampaignsOnPause()) {
            return false;
        }

        if ($campaign->getIsPause()) {
            return false;
        }

        if (!in_array($carrier, $campaign->getCarriers()->getValues())) {
            return false;
        }

        return true;
    }
}