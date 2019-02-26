<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country entity
 */
class Country
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $isoNumeric;

    /**
     * @var string
     */
    private $isoAlpha3;


    public function __toString ()
    {
        return '(' . $this->getCountryCode() . ') ' . $this->getCountryName();
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return Country
     */
    public function setCountryCode ($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode ()
    {
        return $this->countryCode;
    }

    /**
     * Set countryName
     *
     * @param string $countryName
     *
     * @return Country
     */
    public function setCountryName ($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get countryName
     *
     * @return string
     */
    public function getCountryName ()
    {
        return $this->countryName;
    }

    /**
     * Set currencyCode
     *
     * @param string $currencyCode
     *
     * @return Country
     */
    public function setCurrencyCode ($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCode ()
    {
        return $this->currencyCode;
    }

    /**
     * Set isoNumeric
     *
     * @param string $isoNumeric
     *
     * @return Country
     */
    public function setIsoNumeric ($isoNumeric)
    {
        $this->isoNumeric = $isoNumeric;

        return $this;
    }

    /**
     * Get isoNumeric
     *
     * @return string
     */
    public function getIsoNumeric ()
    {
        return $this->isoNumeric;
    }

    /**
     * Set isoAlpha3
     *
     * @param string $isoAlpha3
     *
     * @return Country
     */
    public function setIsoAlpha3 ($isoAlpha3)
    {
        $this->isoAlpha3 = $isoAlpha3;

        return $this;
    }

    /**
     * Get isoAlpha3
     *
     * @return string
     */
    public function getIsoAlpha3 ()
    {
        return $this->isoAlpha3;
    }

    /**
     * Country constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->deactivatedGames = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

}
