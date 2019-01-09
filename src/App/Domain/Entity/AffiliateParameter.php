<?php

namespace App\Domain\Entity;



class AffiliateParameter
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
    public function getUuid(): string
    {
        return $this->uuid;
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