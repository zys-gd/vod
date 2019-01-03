<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Campaign;
use App\Domain\Entity\Interfaces\HasUuid;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Validator\Constraints\ContainsConstraints;
use Doctrine\Common\Collections\Collection;
use http\Url;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Carrier
 * @package App\Domain\Entity
 */
class Carrier implements HasUuid
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $id_carrier;

    /**
     * @var Collection
     */
    private $categoryCarrierOverrides;

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
    private $trial_initializer = 'carrier';

    /**
     * Amount of credits for users trial subscription
     * @var integer
     */
    private $trial_credits = 2;

    /**
     * Trial period in days
     * @var integer
     */
    private $trial_period = 0;

    /**
     * User subscription days amount
     * @var integer
     */
    private $subscription_period = 7;


    /**
     *  Amount of credits for users subscription
     * @var integer
     */
    private $subscription_credits = 2;


    /**
     * @var $operator_id
     */
    private $operator_id = null;

    /**
     * @var array
     */
    private $deactivatedGames;

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

    /** @var string */
    private $uuid = null;
    /**
     * @var bool
     */
    private $isCapAlertDispatch  = false;


    /**
     * Carrier constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->categoryCarrierOverrides = new ArrayCollection();
        $this->deactivatedGames = new ArrayCollection();
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
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param mixed $operator_id
     */
    public function setOperatorId($operator_id)
    {
        $this->operator_id = $operator_id;
    }

    /**
     * @return mixed
     */
    public function getOperatorId()
    {
        return $this->operator_id;
    }

    /**
     * @return mixed
     */
    public function getTrialInitializer()
    {
        return $this->trial_initializer;
    }

    /**
     * @param mixed $trial_initializer
     */
    public function setTrialInitializer($trial_initializer)
    {
        $this->trial_initializer = $trial_initializer;
    }

    /**
     * @return int
     */
    public function getTrialPeriod(): int
    {
        return $this->trial_period;
    }

    /**
     * @param int $trial_period
     */
    public function setTrialPeriod(int $trial_period)
    {
        $this->trial_period = $trial_period;
    }

    /**
     * @return int
     */
    public function getTrialCredits(): int
    {
        return $this->trial_credits;
    }

    /**
     * @param int $trial_credits
     */
    public function setTrialCredits(int $trial_credits)
    {
        $this->trial_credits = $trial_credits;
    }

    /**
     * @return int
     */
    public function getSubscriptionPeriod(): int
    {
        return $this->subscription_period;
    }

    /**
     * @param int $subscription_period
     */
    public function setSubscriptionPeriod(int $subscription_period)
    {
        $this->subscription_period = $subscription_period;
    }

    /**
     * @return int
     */
    public function getSubscriptionCredits(): int
    {
        return $this->subscription_credits;
    }

    /**
     * @param int $subscription_credits
     */
    public function setSubscriptionCredits(int $subscription_credits)
    {
        $this->subscription_credits = $subscription_credits;
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
     * Set id
     *
     * @param string $id
     *
     * @return Carrier
     */
    public function setId($id)
    {
        $this->id = $id;

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
    public function setIdCarrier($idCarrier)
    {
        $this->id_carrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return (int)$this->id_carrier;
    }

    /**
     * Add categoryCarrierOverride
     *
     * @param CategoryCarrierOverride $categoryCarrierOverride
     *
     * @return Carrier
     */
    public function addCategoryCarrierOverride(CategoryCarrierOverride $categoryCarrierOverride)
    {
        $this->categoryCarrierOverrides[] = $categoryCarrierOverride;

        return $this;
    }

    /**
     * Remove categoryCarrierOverride
     *
     * @param CategoryCarrierOverride $categoryCarrierOverride
     */
    public function removeCategoryCarrierOverride(CategoryCarrierOverride $categoryCarrierOverride)
    {
        $this->categoryCarrierOverrides->removeElement($categoryCarrierOverride);
    }

    /**
     * Get categoryCarrierOverrides
     *
     * @return Collection
     */
    public function getCategoryCarrierOverrides()
    {
        return $this->categoryCarrierOverrides;
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
    public function getCountryCode()
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
    public function getIsp()
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
     * @return Collection
     */
    public function getDeactivatedGames(): Collection
    {
        return $this->deactivatedGames;
    }

    /**
     * @param array $deactivatedGames
     */
    public function setDeactivatedGames($deactivatedGames)
    {
        $this->deactivatedGames = $deactivatedGames;
    }

    /**
     * Add game to deactivated carriers array
     *
     * @param Game $deactivatedGames
     *
     * @return Carrier
     */
    public function addDeactivatedGames(Game $deactivatedGames)
    {
        $this->deactivatedGames[] = $deactivatedGames;

        return $this;
    }

    /**
     * Remove game from deactivated array of carriers
     *
     * @param Game $deactivatedGames
     */
    public function removeDeactivatedGames(Game $deactivatedGames)
    {
        $this->deactivatedGames->removeElement($deactivatedGames);
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
     * @var \App\Domain\Entity\Languages
     */
    private $default_language;


    /**
     * Set defaultLanguage
     *
     * @param \App\Domain\Entity\Languages $defaultLanguage
     *
     * @return Carrier
     */
    public function setDefaultLanguage(\App\Domain\Entity\Languages $defaultLanguage = null)
    {
        $this->default_language = $defaultLanguage;

        return $this;
    }

    /**
     * Get defaultLanguage
     *
     * @return \App\Domain\Entity\Languages
     */
    public function getDefaultLanguage()
    {
        return $this->default_language;
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
