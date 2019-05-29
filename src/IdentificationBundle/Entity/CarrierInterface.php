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

    /**
     * @return int|null
     */
    public function getNumberOfAllowedSubscriptionsByConstraint(): ?int;

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string;

    /**
     * @param bool $isCapAlertDispatch
     *
     * @return CarrierInterface
     */
    public function setIsCapAlertDispatch(bool $isCapAlertDispatch): self;

    /**
     * @return bool
     */
    public function getIsCapAlertDispatch(): bool;

    /**
     * @param \DateTime $flushDate
     *
     * @return CarrierInterface
     */
    public function setFlushDate(\DateTime $flushDate): self;

    /**
     * @return bool
     */
    public function getIsCampaignsOnPause(): bool;

    /**
     * Get resubAllowed
     *
     * @return boolean
     */
    public function getResubAllowed(): bool;

    public function getSubscribeAttempts();

}