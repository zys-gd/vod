<?php

namespace IdentificationBundle\Entity;


class User
{

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
    private $urlId;

    /**
     * @var string
     */
    private $shortUrlId;

    private $identificationProcessId;

    private $identificationToken;

    /**
     * User constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->added = new \DateTime();
        $this->uuid  = $uuid;
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
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @return integer
     */
    public function getCarrierId()
    {
        return $this->carrier->getUuid();
    }

    /**
     * @return string
     */
    public function getExternalCarrierId()
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
    public function getUrlId()
    {
        return $this->urlId;
    }

    /**
     * @param string $urlId
     */
    public function setUrlId($urlId)
    {
        $this->urlId = $urlId;
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



}