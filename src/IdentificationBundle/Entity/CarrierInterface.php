<?php

namespace IdentificationBundle\Entity;

interface CarrierInterface
{
    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @return string|null
     */
    public function getIsp(): ?string;

    /**
     * @return int
     */
    public function getBillingCarrierId(): int;

    /**
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * @return int
     */
    public function getOperatorId(): int ;
}