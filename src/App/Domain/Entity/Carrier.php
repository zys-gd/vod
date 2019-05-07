<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use IdentificationBundle\Entity\CarrierInterface;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * Class Carrier
 * @package App\Domain\Entity
 */
class Carrier implements CarrierInterface, HasUuid
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
     * @var integer
     */
    private $billingCarrierId;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $isp;

    /**
     * @var boolean
     */
    private $published = false;

    /**
     * @var boolean
     */
    private $lpOtp = false;

    /**
     * Is carrier supports wi-fi flow identification via sms pin code
     * @var bool
     */
    private $pinIdentSupport;

    /**
     * Can be store|carrier
     * @var string
     */
    private $trialInitializer = 'carrier';

    /**
     * Trial period in days
     * @var integer
     */
    private $trialPeriod = 0;

    /**
     * User subscription days amount
     * @var integer
     */
    private $subscriptionPeriod = 7;

    /**
     * @var $operatorId
     */
    private $operatorId = null;

    /**
     * @var boolean
     */
    private $resubAllowed = false;

    /**
     * @var boolean
     */
    private $isCampaignsOnPause = false;

    /**
     * @var integer
     */
    private $numberOfAllowedSubscription = null;

    /**
     * @var integer
     */
    private $numberOfAllowedSubscriptionsByConstraint = null;

    /**
     * Counter of subscriptions by carrier, field is not mapped to db,
     * created for displaying counter from redis in admin panel
     *
     * @var int
     */
    private $counter;

    /**
     * @var string
     */
    private $redirectUrl = null;

    /**
     * @var \DateTime
     */
    private $flushDate;

    /**
     * @var bool
     */
    private $isUnlimitedSubscriptionAttemptsAllowed = true;

    /**
     * @var Campaign[] | ArrayCollection
     */
    private $campaigns;

    /**
     * @var bool
     */
    private $isCapAlertDispatch  = false;

    /**
     * @var \App\Domain\Entity\Language
     */
    private $defaultLanguage;

    /**
     * Carrier constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->campaigns = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?? '';
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
     * @return Carrier
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @param mixed $operatorId
     *
     * @return Carrier
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOperatorId(): int
    {
        return $this->operatorId;
    }

    /**
     * @return mixed
     */
    public function getTrialInitializer()
    {
        return $this->trialInitializer;
    }

    /**
     * @param mixed $trialInitializer
     *
     * @return Carrier
     */
    public function setTrialInitializer($trialInitializer): Carrier
    {
        $this->trialInitializer = $trialInitializer;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrialPeriod(): int
    {
        return $this->trialPeriod;
    }

    /**
     * @param int $trialPeriod
     *
     * @return Carrier
     */
    public function setTrialPeriod(int $trialPeriod)
    {
        $this->trialPeriod = $trialPeriod;

        return $this;
    }

    /**
     * @return int
     */
    public function getSubscriptionPeriod(): int
    {
        return $this->subscriptionPeriod;
    }

    /**
     * @param int $subscriptionPeriod
     *
     * @return Carrier
     */
    public function setSubscriptionPeriod(int $subscriptionPeriod): Carrier
    {
        $this->subscriptionPeriod = $subscriptionPeriod;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Carrier
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return Carrier
     */
    public function setBillingCarrierId($idCarrier)
    {
        $this->billingCarrierId = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getBillingCarrierId(): int
    {
        return (int)$this->billingCarrierId;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $isp
     *
     * @return Carrier
     */
    public function setIsp($isp): self
    {
        $this->isp = $isp;

        return $this;
    }

    /**
     * Get isp
     *
     * @return string|null
     */
    public function getIsp(): ?string
    {
        return $this->isp;
    }

    /**
     * @param $published
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }


    /**
     * @param bool $lpOtp
     *
     * @return Carrier
     */
    public function setLpOtp($lpOtp): Carrier
    {
        $this->lpOtp = $lpOtp;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLpOtp()
    {
        return $this->lpOtp;
    }


    /**
     * Get published
     *
     * @return boolean
     */
    public function getLpOtp()
    {
        return $this->lpOtp;
    }

    /**
     * @return bool
     */
    public function getPinIdentSupport()
    {
        return $this->pinIdentSupport;
    }

    /**
     * Alias
     * @return bool
     */
    public function isPinIdentSupport()
    {
        return $this->getPinIdentSupport();
    }

    /**
     * @param bool $pinIdentSupport
     *
     * @return Carrier
     */
    public function setPinIdentSupport(bool $pinIdentSupport): Carrier
    {
        $this->pinIdentSupport = $pinIdentSupport;

        return $this;
    }

    /**
     * Set resubAllowed
     *
     * @param boolean $resubAllowed
     *
     * @return Carrier
     */
    public function setResubAllowed($resubAllowed)
    {
        $this->resubAllowed = $resubAllowed;

        return $this;
    }

    /**
     * Get resubAllowed
     *
     * @return boolean
     */
    public function getResubAllowed(): bool
    {
        return $this->resubAllowed;
    }

    /**
     * Set defaultLanguage
     *
     * @param Language $defaultLanguage
     *
     * @return Carrier
     */
    public function setDefaultLanguage(Language $defaultLanguage = null)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * Get defaultLanguage
     *
     * @return Language
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Get isCampaignsOnPause
     *
     * @return boolean
     */
    public function getIsCampaignsOnPause(): bool
    {
        return $this->isCampaignsOnPause;
    }

    /**
     * @param $isCampaignsOnPause
     *
     * @return Carrier
     */
    public function setIsCampaignsOnPause($isCampaignsOnPause)
    {
        $this->isCampaignsOnPause = $isCampaignsOnPause;

        return $this;
    }

    /**
     * @param integer $numberOfAllowedSubscription
     *
     * @return Carrier
     */
    public function setNumberOfAllowedSubscription($numberOfAllowedSubscription)
    {
        $this->numberOfAllowedSubscription = $numberOfAllowedSubscription;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumberOfAllowedSubscription()
    {
        return $this->numberOfAllowedSubscription;
    }

    /**
     * @param bool $isUnlimitedSubscriptionAttemptsAllowed
     */
    public function setIsUnlimitedSubscriptionAttemptsAllowed(bool $isUnlimitedSubscriptionAttemptsAllowed)
    {
        $this->isUnlimitedSubscriptionAttemptsAllowed = $isUnlimitedSubscriptionAttemptsAllowed;
    }

    /**
     * @return bool
     */
    public function isUnlimitedSubscriptionAttemptsAllowed(): bool
    {
        return $this->isUnlimitedSubscriptionAttemptsAllowed;
    }

    /**
     * Set numberOfAllowedSubscriptionsByConstraint
     *
     * @param integer $numberOfAllowedSubscriptionsByConstraint
     *
     * @return Carrier
     */
    public function setNumberOfAllowedSubscriptionsByConstraint($numberOfAllowedSubscriptionsByConstraint)
    {
        $this->numberOfAllowedSubscriptionsByConstraint = $numberOfAllowedSubscriptionsByConstraint;

        return $this;
    }

    /**
     * Get numberOfAllowedSubscriptionsByConstraint
     *
     * @return int|null
     */
    public function getNumberOfAllowedSubscriptionsByConstraint(): ?int
    {
        return $this->numberOfAllowedSubscriptionsByConstraint;
    }

    /**
     * Set redirectUrl
     *
     * @param string $redirectUrl
     *
     * @return Carrier
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get redirectUrl
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @param \DateTime $flushDate
     *
     * @return CarrierInterface
     */
    public function setFlushDate($flushDate): CarrierInterface
    {
        $this->flushDate = $flushDate;

        return $this;
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getFlushDate()
    {
        return $this->flushDate;
    }

    /**
     * Add campaign
     *
     * @param Campaign $campaign
     *
     * @return Carrier
     */
    public function addCampaign(Campaign $campaign)
    {
        $this->campaigns[] = $campaign;

        return $this;
    }

    /**
     * Remove campaign
     *
     * @param Campaign $campaign
     * @return Carrier
     */
    public function removeCampaign(Campaign $campaign)
    {
        $this->campaigns->removeElement($campaign);

        return $this;
    }

    /**
     * Get campaigns
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }


    /**
     * @param bool $isCapAlertDispatch
     *
     * @return CarrierInterface
     */
    public function setIsCapAlertDispatch(bool $isCapAlertDispatch): CarrierInterface
    {
        $this->isCapAlertDispatch = $isCapAlertDispatch;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCapAlertDispatch(): bool
    {
        return $this->isCapAlertDispatch;
    }

    /**
     * Setter is related to field which is not mapped to db and created
     * for displaying counter from redis in admin panel
     *
     * @param int $counter
     *
     * @return CarrierInterface
     */
    public function setCounter(int $counter): CarrierInterface
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Getter is related to field which is not mapped to db and created
     * for displaying counter from redis in admin panel
     *
     * @return int|null
     */
    public function getCounter(): ?int
    {
        return isset($this->counter) ? $this->counter : null;
    }

}
