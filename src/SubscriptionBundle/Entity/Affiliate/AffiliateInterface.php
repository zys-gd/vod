<?php

namespace SubscriptionBundle\Entity\Affiliate;

/**
 * Interface AffiliateInterface
 */
interface AffiliateInterface
{
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
    public function getConstantsList(): array;

    /**
     * @return string|null
     */
    public function getSubPriceName(): ?string;

}