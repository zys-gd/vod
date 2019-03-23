<?php

namespace SubscriptionBundle\Entity\Affiliate;

use IdentificationBundle\Entity\CarrierInterface;

/**
 * Campaign
 */
class ConstraintByAffiliate
{
    /**
     * Cap types
     */
    const CAP_TYPE_SUBSCRIBE = 'subscribe';
    const CAP_TYPE_VISIT = 'visit';

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var AffiliateInterface
     */
    private $affiliate;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var integer
     */
    private $numberOfActions;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @var integer
     */
    private $counter = 0;

    /**
     * @var \DateTime
     */
    private $flushDate;

    /**
     * @var bool
     */
    private $isCapAlertDispatch = false;

    /**
     * @var string
     */
    private $capType;

    /**
     * ConstraintByAffiliate constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->capType = self::CAP_TYPE_SUBSCRIBE;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return ConstraintByAffiliate
     */
    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @param AffiliateInterface $affiliate
     *
     * @return ConstraintByAffiliate
     */
    public function setAffiliate(AffiliateInterface $affiliate): self
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * @return AffiliateInterface|null
     */
    public function getAffiliate(): ?AffiliateInterface
    {
        return $this->affiliate;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return ConstraintByAffiliate
     */
    public function setCarrier(CarrierInterface $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return CarrierInterface|null
     */
    public function getCarrier(): ?CarrierInterface
    {
        return $this->carrier;
    }

    /**
     * @param bool $isCapAlertDispatch
     *
     * @return ConstraintByAffiliate
     */
    public function setIsCapAlertDispatch(bool $isCapAlertDispatch): self
    {
        $this->isCapAlertDispatch = $isCapAlertDispatch;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCapAlertDispatch(): bool
    {
        return $this->isCapAlertDispatch;
    }

    /**
     * @param int $numberOfActions
     *
     * @return ConstraintByAffiliate
     */
    public function setNumberOfActions(int $numberOfActions): self
    {
        $this->numberOfActions = $numberOfActions;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberOfActions(): ?int
    {
        return $this->numberOfActions;
    }

    /**
     * @param string $redirectUrl
     *
     * @return ConstraintByAffiliate
     */
    public function setRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @param int $counter
     *
     * @return ConstraintByAffiliate
     */
    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @param \DateTime $flushDate
     *
     * @return ConstraintByAffiliate
     */
    public function setFlushDate(\DateTime $flushDate): self
    {
        $this->flushDate = $flushDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getFlushDate(): ?\DateTime
    {
        return $this->flushDate;
    }

    /**
     * @param string $capType
     *
     * @return $this
     */
    public function setCapType(string $capType): self
    {
        $this->capType = $capType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCapType(): string
    {
        return $this->capType;
    }
}
