<?php

namespace IdentificationBundle\Entity;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;

/**
 * Class User
 */
class User
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $country;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var \DateTime
     */
    private $added;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var array
     */
    private $affiliateToken;

    /**
     * @var string
     */
    private $shortUrlId;

    /**
     * @var string
     */
    private $identificationProcessId;

    /**
     * @var string
     */
    private $identificationToken;

    /**
     * @var string
     */
    private $connectionType;
    /**
     * @var string
     */
    private $identificationUrl;
    /**
     * @var string
     */
    private $deviceManufacturer;
    /**
     * @var string
     */
    private $deviceModel;

    private $languageCode;
    /**
     * User constructor
     *
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->added = new \DateTime();
        $this->uuid  = $uuid;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->identifier ?? '';
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Returns the user's country
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country for current user
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = strtoupper($country);
    }

    public function getAffiliateToken()
    {
        return $this->affiliateToken;
    }

    /**
     * @param string $affiliateToken
     */
    public function setAffiliateToken($affiliateToken)
    {
        $this->affiliateToken = $affiliateToken;
    }

    /**
     * Returns the user's carrier
     * @return CarrierInterface
     */
    public function getCarrier(): CarrierInterface
    {
        return $this->carrier;
    }

    /**
     * @return integer
     */
    public function getCarrierId()
    {
        return $this->carrier->getBillingCarrierId();
    }

    /**
     * @return string
     */
    public function getBillingCarrierId()
    {
        return $this->carrier->getBillingCarrierId();
    }

    /**
     * Sets the carrier for current user
     * @param CarrierInterface $carrier
     */
    public function setCarrier(CarrierInterface $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getShortUrlId()
    {
        return $this->shortUrlId;
    }

    /**
     * @param string $shortUrlId
     */
    public function setShortUrlId($shortUrlId)
    {
        $this->shortUrlId = $shortUrlId;
    }

    /**
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param \DateTime $added
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getIdentificationProcessId(): ?string
    {
        return $this->identificationProcessId;
    }

    /**
     * @param mixed $identificationProcessId
     * @return User
     */
    public function setIdentificationProcessId(string $identificationProcessId = null)
    {
        $this->identificationProcessId = $identificationProcessId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentificationToken()
    {
        return $this->identificationToken;
    }

    /**
     * @param mixed $identificationToken
     * @return User
     */
    public function setIdentificationToken($identificationToken)
    {
        $this->identificationToken = $identificationToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @param mixed $connectionType
     * @return User
     */
    public function setConnectionType($connectionType)
    {
        $this->connectionType = $connectionType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentificationUrl()
    {
        return $this->identificationUrl;
    }

    /**
     * @param mixed $identificationUrl
     * @return User
     */
    public function setIdentificationUrl($identificationUrl)
    {
        $this->identificationUrl = $identificationUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeviceManufacturer()
    {
        return $this->deviceManufacturer;
    }

    /**
     * @param mixed $deviceManufacturer
     * @return User
     */
    public function setDeviceManufacturer($deviceManufacturer)
    {
        $this->deviceManufacturer = $deviceManufacturer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeviceModel()
    {
        return $this->deviceModel;
    }

    /**
     * @param mixed $deviceModel
     * @return User
     */
    public function setDeviceModel($deviceModel)
    {
        $this->deviceModel = $deviceModel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param mixed $languageCode
     */
    public function setLanguageCode($languageCode): void
    {
        $this->languageCode = $languageCode;
    }


}