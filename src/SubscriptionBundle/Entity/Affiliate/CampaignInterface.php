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
     * @return float
     */
    public function getFreeTrialPrice(): float;

    /**
     * @return float
     */
    public function getZeroEurPrice(): float;

    /**
     * @return float
     */
    public function getGeneralPrice(): float;

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
    public function getBgColor(): ?string;

    /**
     * @return mixed
     */
    public function getMainCategory();

    public function isFreeTrialSubscription(): bool;

    /**
     * @return bool
     */
    public function isConfirmationClick(): bool;

    /**
     * @return bool
     */
    public function isConfirmationPopup(): bool;
}