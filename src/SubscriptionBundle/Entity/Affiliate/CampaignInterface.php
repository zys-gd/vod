<?php

namespace SubscriptionBundle\Entity\Affiliate;

use App\Domain\Entity\Affiliate;
use Doctrine\Common\Collections\Collection;

interface CampaignInterface
{

    public function getCampaignToken(): string;

    public function getSub(): string;


    /**
     * Get operator
     *
     * @return Collection
     */
    public function getCarriers(): Collection;

    public function getAffiliate(): ?Affiliate;
}