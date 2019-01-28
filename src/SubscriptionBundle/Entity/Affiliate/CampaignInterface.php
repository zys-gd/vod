<?php

namespace SubscriptionBundle\Entity\Affiliate;

use App\Domain\Entity\Affiliate;
use Doctrine\Common\Collections\Collection;

interface CampaignInterface
{
    /**
     * @return string
     */
    public function getCampaignToken(): string;

    /**
     * @return string|null
     */
    public function getSub(): ?string;

    /**
     * @return Collection
     */
    public function getCarriers(): Collection;

    /**
     * @return Affiliate|null
     */
    public function getAffiliate(): ?Affiliate;
}