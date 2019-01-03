<?php

namespace App\Domain\Entity;

use App\Domain\Entity\CategoryCountryOverride;
use App\Domain\Entity\Interfaces\HasUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Country entity
 */
class Country implements HasUuid
{
    /**
     * @var int
     */
    private $id;

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
     * Get id
     *
     * @return int
     */
    public function getId ()
    {
        return $this->id;
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

    /** @var string */
    private $uuid = null;

    /**
     * Country constructor.
     * @throws \Exception
     */
    public function __construct ()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->deactivatedGames = new ArrayCollection();
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
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

}
