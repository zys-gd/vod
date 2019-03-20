<?php

namespace IdentificationBundle\Entity;


/**
 * Class TestUser
 */
class TestUser
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $msisdn;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var int
     */
    private $status = self::STATUS_APPROVED;

    /**
     * @var \DateTime
     */
    private $addedAt;

    /**
     * @var \DateTime
     */
    private $lastTimeUsedAt;

    /**
     * TestUser constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->addedAt = new \DateTime();
        $this->uuid    = \Ramsey\Uuid\Uuid::uuid4()->toString();
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
     * @return TestUser
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getMsisdn(): string
    {
        return $this->msisdn;
    }

    /**
     * @param mixed $msisdn
     *
     * @return TestUser
     */
    public function setMsisdn($msisdn): TestUser
    {
        $this->msisdn = $msisdn;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return TestUser
     */
    public function setStatus(int $status): TestUser
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt(): \DateTime
    {
        return $this->addedAt;
    }

    /**
     * @param \DateTime $addedAt
     *
     * @return TestUser
     */
    public function setAddedAt(\DateTime $addedAt): TestUser
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier(): CarrierInterface
    {
        return $this->carrier;
    }

    /**
     * @param CarrierInterface $carrier
     * @return TestUser
     */
    public function setCarrier(CarrierInterface $carrier): TestUser
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastTimeUsedAt()
    {
        return $this->lastTimeUsedAt;
    }

    /**
     * @param mixed $lastTimeUsedAt
     *
     * @return TestUser
     */
    public function setLastTimeUsedAt(\DateTimeInterface $lastTimeUsedAt)
    {
        $this->lastTimeUsedAt = $lastTimeUsedAt;

        return $this;
    }
}