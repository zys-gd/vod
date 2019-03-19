<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * Class AffiliateParameter
 */
class AffiliateParameter implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $inputName;

    /**
     * @var string
     */
    private $outputName;

    /**
     * @var Affiliate
     */
    private $affiliate;

    /**
     * AffiliateParameter constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->uuid;
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
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Set input_name
     *
     * @param string $name
     *
     * @return AffiliateParameter
     */
    public function setInputName($name)
    {
        $this->inputName = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getInputName()
    {
        return $this->inputName;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return AffiliateParameter
     */
    public function setOutputName($name)
    {
        $this->outputName = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getOutputName()
    {
        return $this->outputName;
    }
    /**
     * Set affiliate
     *
     * @param Affiliate $affiliate
     *
     * @return AffiliateParameter
     */
    public function setAffiliate($affiliate)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * Get affiliate
     *
     * @return Affiliate
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

}