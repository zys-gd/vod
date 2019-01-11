<?php

namespace IdentificationBundle\Entity;

/**
 * PinCode
 */
class PinCode
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $pin;

    /**
     * @var \DateTime
     */
    private $addedAt;

    /**
     * PinCode constructor.
     */
    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pin
     *
     * @param string $pin
     *
     * @return PinCode
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Get pin
     *
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set addedAt
     *
     * @param \DateTime $addedAt
     *
     * @return PinCode
     */
    public function setAddedAt($addedAt)
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * Get addedAt
     *
     * @return \DateTime
     */
    public function getAddedAt()
    {
        return $this->addedAt;
    }
}

