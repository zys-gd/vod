<?php

namespace IdentificationBundle\Entity;

interface CarrierInterface
{
    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @return string
     */
    public function getName(): ?string;

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
    public function getOperatorId(): int;

    /**
     * @return int
     */
    public function getSubscriptionPeriod(): int;

    /**
     * @return int
     */
    public function getTrialPeriod(): int;
}