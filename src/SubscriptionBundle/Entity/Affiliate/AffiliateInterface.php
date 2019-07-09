<?php

namespace SubscriptionBundle\Entity\Affiliate;

use Doctrine\Common\Collections\Collection;
use IdentificationBundle\Entity\CarrierInterface;

/**
 * Interface AffiliateInterface
 */
interface AffiliateInterface
{
    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return array
     */
    public function getParamsList(): array;

    /**
     * @return array
     */
    public function getInputParamsList(): array;

    /**
     * @return array
     */
    public function getConstantsList(): array;

    /**
     * @return string|null
     */
    public function getPostbackUrl(): ?string;

    /**
     * @return string|null
     */
    public function getSubPriceName(): ?string;

    /**
     * @return Collection
     */
    public function getConstraints(): Collection;

    /**
     * @param string $capType
     * @param int    $billingCarrierId
     *
     * @return ConstraintByAffiliate|null
     */
    public function getConstraint(string $capType, int $billingCarrierId): ?ConstraintByAffiliate;

    /**
     * @return bool
     */
    public function isUniqueFlow(): bool;

    /**
     * @return string|null
     */
    public function getUniqueParameter(): ?string;
}