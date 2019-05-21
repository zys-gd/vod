<?php

namespace IdentificationBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class TestUser
 *
 * @UniqueEntity(
 *     fields={"userIdentifier"},
 *     message = "This user is already in use"
 * )
 */
class TestUser
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $userIdentifier;

    /**
     * @var CarrierInterface
     */
    private $carrier;

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
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->addedAt = new \DateTime();
        $this->uuid    = $uuid;
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
     * @return string|null
     */
    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @param string $userIdentifier
     *
     * @return TestUser
     */
    public function setUserIdentifier(string $userIdentifier): self
    {
        $this->userIdentifier = $userIdentifier;

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
    public function setAddedAt(\DateTime $addedAt): self
    {
        $this->addedAt = $addedAt;

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
     * @param CarrierInterface $carrier
     *
     * @return TestUser
     */
    public function setCarrier(CarrierInterface $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastTimeUsedAt(): \DateTime
    {
        return $this->lastTimeUsedAt;
    }

    /**
     * @param mixed $lastTimeUsedAt
     *
     * @return TestUser
     */
    public function setLastTimeUsedAt(\DateTimeInterface $lastTimeUsedAt): self
    {
        $this->lastTimeUsedAt = $lastTimeUsedAt;

        return $this;
    }
}