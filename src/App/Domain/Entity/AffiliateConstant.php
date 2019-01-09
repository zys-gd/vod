<?php

namespace App\Domain\Entity;

class AffiliateConstant
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var Affiliate
     */
    private $affiliate;

    /**
     * AffiliateConstant constructor.
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
     * @return AffiliateConstant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $value
     *
     * @return AffiliateConstant
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Set affiliate
     *
     * @param Affiliate $affiliate
     *
     * @return AffiliateConstant
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