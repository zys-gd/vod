<?php

namespace App\Domain\Service\LandingPageAccessor\Accessors;

use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\CarrierInterface;

/**
 * Class VisitAccessorByCampaign
 */
class VisitAccessorByCampaign
{
    /**
     * @var string
     */
    private $defaultRedirectUrl;

    /**
     * VisitAccessorByCampaign constructor
     *
     * @param string $defaultRedirectUrl
     */
    public function __construct(string $defaultRedirectUrl)
    {
        $this->defaultRedirectUrl = $defaultRedirectUrl;
    }

    /**
     * @param Campaign $campaign
     * @param CarrierInterface $carrier
     *
     * @return string|null
     */
    public function canVisitFromCampaign(Campaign $campaign, CarrierInterface $carrier): ?string
    {
        if ($carrier->getIsCampaignsOnPause()) {
            return $carrier->getRedirectUrl() ?? $this->defaultRedirectUrl;
        }

        $isCarrierSuitable = in_array($carrier, $campaign->getCarriers()->getValues());

        if ($campaign->getIsPause() || !$isCarrierSuitable) {
            return $this->defaultRedirectUrl;
        }

        return null;
    }
}