<?php

namespace IdentificationBundle\Repository;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;

/**
 * Interface CarrierRepositoryInterface
 */
interface CarrierRepositoryInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return CarrierInterface|null
     */
    public function findOneByBillingId(int $billingCarrierId): ?CarrierInterface;

    /**
     * @return CarrierInterface[]
     */
    public function findEnabledCarriers(): array;

    /**
     * @return array
     */
    public function findAll();

    /**
     * @return array
     */
    public function findEnabledCarriersCountries(): array;
}