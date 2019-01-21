<?php

namespace SubscriptionBundle\Entity\Affiliate;

use App\Domain\Entity\Affiliate;
use Doctrine\Common\Collections\Collection;

interface CampaignInterface
{
    /**
     * Get operator
     *
     * @return Collection
     */
    public function getCarriers(): Collection;

    public function getAffiliate(): ?Affiliate;
}