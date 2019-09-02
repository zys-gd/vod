<?php

namespace SubscriptionBundle\Entity\Affiliate;

use Doctrine\Common\Collections\Collection;

interface CampaignInterface
{
    /**
     * @return string
     */
    public function getUuid(): string;

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
     * @return AffiliateInterface|null
     */
    public function getAffiliate(): ?AffiliateInterface;

    /**
     * @return bool
     */
    public function isZeroCreditSubAvailable(): bool;

    /**
     * @return bool
     */
    public function isConfirmationClick(): bool;
}