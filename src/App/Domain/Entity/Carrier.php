<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Campaign;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Validator\Constraints\ContainsConstraints;
use Doctrine\Common\Collections\Collection;
use http\Url;
use IdentificationBundle\Entity\CarrierInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Carrier
 * @package App\Domain\Entity
 */
class Carrier implements CarrierInterface
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
     * @var string
     */
    private $redirectUrl = null;

    /**
     * @var integer
     */
    private $counter = 0;

    /**
     * @var \DateTime
     */
    private $flushDate;

    /**
     * @var bool
     */
    private $isUnlimitedSubscriptionAttemptsAllowed = true;

    /**
     * @var bool
     */
    private $isCaptcha = false;

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
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?? '';
    }

    /**
     * @param mixed $operatorId
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
    }

    /**
     * @return mixed
     */
    public function getOperatorId()
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
     */
    public function setTrialInitializer($trialInitializer)
    {
        $this->trialInitializer = $trialInitializer;
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
     */
    public function setTrialPeriod(int $trialPeriod)
    {
        $this->trialPeriod = $trialPeriod;
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
     */
    public function setSubscriptionPeriod(int $subscriptionPeriod)
    {
        $this->subscriptionPeriod = $subscriptionPeriod;
    }

    /**
     * Set id
     *
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
    public function getName()
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
     * Set isp
     *
     * @param string $isp
     */
    public function setIsp($isp)
    {
        $this->isp = $isp;
    }

    /**
     * Get isp
     *
     * @return string
     */
    public function getIsp(): string
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
     */
    public function setLpOtp($lpOtp)
    {
        $this->lpOtp = $lpOtp;
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
    public function getResubAllowed()
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
    public function getIsCampaignsOnPause()
    {
        return $this->isCampaignsOnPause;
    }

    /**
     * Set isCampaignsOnPause
     *
     * @var $isCampaignsOnPause boolean
     */
    public function setIsCampaignsOnPause($isCampaignsOnPause)
    {
        $this->isCampaignsOnPause = $isCampaignsOnPause;

        return $this;
    }

    /**
     * @param integer $numberOfAllowedSubscription
     */
    public function setNumberOfAllowedSubscription($numberOfAllowedSubscription)
    {
        $this->numberOfAllowedSubscription = $numberOfAllowedSubscription;
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
     * @param bool $isCaptcha
     */
    public function setIsCaptcha(bool $isCaptcha)
    {
        $this->isCaptcha = $isCaptcha;
    }

    /**
     * @return bool
     */
    public function isCaptcha(): bool
    {
        return $this->isCaptcha;
    }

    /**
     * Set numberOfAllowedSubscriptionsByConstraint
     *
     * @param integer $numberOfAllowedSubscriptionsByConstraint
     *
     * @return Carrier
     */
    public function setnumberOfAllowedSubscriptionsByConstraint($numberOfAllowedSubscriptionsByConstraint)
    {
        $this->numberOfAllowedSubscriptionsByConstraint = $numberOfAllowedSubscriptionsByConstraint;

        return $this;
    }

    /**
     * Get numberOfAllowedSubscriptionsByConstraint
     *
     * @return integer
     */
    public function getnumberOfAllowedSubscriptionsByConstraint()
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
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set counter
     *
     * @param integer $counter
     *
     * @return Carrier
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Set flushDate
     *
     * @param \DateTime $flushDate
     *
     * @return Carrier
     */
    public function setFlushDate($flushDate)
    {
        $this->flushDate = $flushDate;

        return $this;
    }

    /**
     * Get flushDate
     *
     * @return \DateTime
     */
    public function getFlushDate()
    {
        if(is_null($this->flushDate)){

            $this->flushDate = new \DateTime('now');

        }
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
     */
    public function setIsCapAlertDispatch(bool $isCapAlertDispatch)
    {
        $this->isCapAlertDispatch = $isCapAlertDispatch;
    }

    /**
     * @return bool
     */
    public function isCapAlertDispatch(): bool
    {
        return $this->isCapAlertDispatch;
    }

}
