<?php

namespace SubscriptionBundle\Entity;

use App\Domain\Entity\Country;
use Doctrine\Common\Collections\ArrayCollection;
use IdentificationBundle\Entity\CarrierInterface;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * Class SubscriptionPack
 */
class SubscriptionPack implements HasUuid
{
    const DAILY = 1;
    const WEEKLY = 7;
    const MONTHLY = 28;
    const CUSTOM_PERIODICITY = 8;

    const PERIODICITY = [
        'DAILY'              => self::DAILY,
        'WEEKLY'             => self::WEEKLY,
        'MONTHLY'            => self::MONTHLY,
        'CUSTOM_PERIODICITY' => self::CUSTOM_PERIODICITY,
    ];

    const ACTIVE_SUBSCRIPTION_PACK = 1;
    const INACTIVE_SUBSCRIPTION_PACK = 0;
    const STATUSES = [
        'ACTIVE'   => self::ACTIVE_SUBSCRIPTION_PACK,
        'INACTIVE' => self::INACTIVE_SUBSCRIPTION_PACK,
    ];

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $isResubAllowed;

    /**
     * @var Subscription
     */
    private $subscriptions;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var integer
     */
    private $billingCarrierId;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var  string
     */
    private $tier;

    /**
     * @var integer
     */
    private $tierId;

    /**
     */
    private $tierPrice;

    /**
     * @var string
     */
    private $tierCurrency;

    /**
     * @var string
     */
    private $displayCurrency = '';

    /**
     * @var int
     */
    private $credits = 0;

    /**
     * @var int
     */
    private $periodicity = 7;

    /**
     * @var int
     */
    private $customRenewPeriod = 0;

    /**
     * @var int
     */
    private $gracePeriod = 0;

    /**
     * @var bool
     */
    private $unlimitedGracePeriod = false;

    /**
     * @var \DateTime
     */
    private $preferredRenewalStart;

    /**
     * @var \DateTime
     */
    private $preferredRenewalEnd;

    /**
     * @var string
     */
    private $welcomeSMSText = "";

    /**
     * @var string
     */
    private $renewalSMSText = "";

    /**
     * @var string
     */
    private $unsubscribeSMSText = "";

    /**
     * @var  string
     */
    private $buyStrategy;

    /**
     * @var integer
     */
    private $buyStrategyId;

    /**
     * @var  string
     */
    private $renewStrategy;

    /**
     * @var integer
     */
    private $renewStrategyId;

    /**
     * @var boolean
     */
    private $unlimited = 0;

    /**
     * @var boolean
     */
    private $firstSubscriptionPeriodIsFreeMultiple = false;

    /**
     * @var boolean
     */
    private $firstSubscriptionPeriodIsFree = false;

    /**
     * @var boolean
     */
    private $allowBonusCredit = false;

    /**
     * @var boolean
     */
    private $allowBonusCreditMultiple = false;

    /**
     * @var integer
     */
    private $bonusCredit = 0;

    /**
     * @var boolean
     */
    private $providerManagedSubscriptions;

    /**
     * @var \DateTime $created
     */
    private $created;

    /**
     * @var \DateTime $updated
     */
    private $updated;

    /** @var bool  */
    private $zeroCreditSubAvailable = false;

    /**
     * SubscriptionPack constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid          = $uuid;
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * Returns the subscription pack name
     *
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
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SubscriptionPack
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
     * Set description
     *
     * @param string $description
     *
     * @return SubscriptionPack
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Subscription
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param Subscription $subscriptions
     */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::ACTIVE_SUBSCRIPTION_PACK;
    }

    /**
     * @return bool
     */
    public function isInActive()
    {
        return $this->status === self::INACTIVE_SUBSCRIPTION_PACK;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $isResubAllowed
     */
    public function setIsResubAllowed(bool $isResubAllowed)
    {
        $this->isResubAllowed = $isResubAllowed;
    }

    /**
     * @return bool|null
     */
    public function isResubAllowed()
    {
        return $this->isResubAllowed;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @param CarrierInterface $carrier
     */
    public function setCarrier(CarrierInterface $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return CarrierInterface string
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param $tierPrice
     */
    public function setTierPrice($tierPrice)
    {
        $this->tierPrice = $tierPrice;
    }

    /**
     * @return float
     */
    public function getTierPrice()
    {
        return $this->tierPrice;
    }

    /**
     * @return string
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @param string $tier
     */
    public function setTier($tier)
    {
        $this->tier = $tier;
    }

    /**
     * @return int
     */
    public function getTierId()
    {
        return $this->tierId;
    }

    /**
     * @param int $tierId
     *
     * @return SubscriptionPack
     */
    public function setTierId($tierId)
    {
        $this->tierId = $tierId;

        return $this;
    }

    /**
     * @param string $tierCurrency
     */
    public function setTierCurrency($tierCurrency)
    {
        $this->tierCurrency = $tierCurrency;
    }

    /**
     * @return string
     */
    public function getTierCurrency()
    {
        return $this->tierCurrency;
    }

    /**
     * @param string $displayCurrency
     */
    public function setDisplayCurrency($displayCurrency)
    {
        $this->displayCurrency = $displayCurrency;
    }

    /**
     * @return string
     */
    public function getDisplayCurrency()
    {
        return $this->displayCurrency;
    }

    /**
     * @return int
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * @param int $credits
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
    }

    /**
     * @return int
     */
    public function getPeriodicity()
    {
        return $this->periodicity;
    }

    /**
     * @param int $periodicity
     */
    public function setPeriodicity($periodicity)
    {
        $this->periodicity = $periodicity;
    }

    /**
     * @return int
     */
    public function getCustomRenewPeriod()
    {
        return $this->customRenewPeriod;
    }

    /**
     * @param int $customRenewPeriod
     */
    public function setCustomRenewPeriod($customRenewPeriod)
    {
        $this->customRenewPeriod = $customRenewPeriod;
    }

    /**
     * @return int
     */
    public function getGracePeriod()
    {
        return $this->gracePeriod;
    }

    /**
     * @param int $gracePeriod
     */
    public function setGracePeriod($gracePeriod)
    {
        $this->gracePeriod = $gracePeriod;
    }

    /**
     * @return mixed
     */
    public function hasUnlimitedGracePeriod()
    {
        return $this->unlimitedGracePeriod;
    }

    /**
     * @param mixed $unlimitedGracePeriod
     */
    public function setUnlimitedGracePeriod($unlimitedGracePeriod)
    {
        $this->unlimitedGracePeriod = $unlimitedGracePeriod;
    }

    /**
     * @return \DateTime
     */
    public function getPreferredRenewalStart()
    {
        return $this->preferredRenewalStart;
    }

    /**
     * @param \DateTime $preferredRenewalStart
     */
    public function setPreferredRenewalStart($preferredRenewalStart)
    {
        $this->preferredRenewalStart = $preferredRenewalStart;
    }

    /**
     * @return \DateTime
     */
    public function getPreferredRenewalEnd()
    {
        return $this->preferredRenewalEnd;
    }

    /**
     * @param \DateTime $preferredRenewalEnd
     */
    public function setPreferredRenewalEnd($preferredRenewalEnd)
    {
        $this->preferredRenewalEnd = $preferredRenewalEnd;
    }

    /**
     * @return string
     */
    public function getWelcomeSMSText()
    {
        return $this->welcomeSMSText;
    }

    /**
     * @param string $welcomeSMSText
     */
    public function setWelcomeSMSText($welcomeSMSText)
    {
        $this->welcomeSMSText = $welcomeSMSText;
    }

    /**
     * @return string
     */
    public function getRenewalSMSText()
    {
        return $this->renewalSMSText;
    }

    /**
     * @param string $renewalSMSText
     */
    public function setRenewalSMSText($renewalSMSText)
    {
        $this->renewalSMSText = $renewalSMSText;
    }

    /**
     * @return string
     */
    public function getUnsubscribeSMSText()
    {
        return $this->unsubscribeSMSText;
    }

    /**
     * @param string $unsubscribeSMSText
     */
    public function setUnsubscribeSMSText($unsubscribeSMSText)
    {
        $this->unsubscribeSMSText = $unsubscribeSMSText;
    }

    /**
     * @return int
     */
    public function getBuyStrategyId()
    {
        return $this->buyStrategyId;
    }

    /**
     * @param int $buyStrategyId
     */
    public function setBuyStrategyId($buyStrategyId)
    {
        $this->buyStrategyId = $buyStrategyId;
    }

    /**
     * @return int
     */
    public function getRenewStrategyId()
    {
        return $this->renewStrategyId;
    }

    /**
     * @param int $renewStrategyId
     */
    public function setRenewStrategyId($renewStrategyId)
    {
        $this->renewStrategyId = $renewStrategyId;
    }

    /**
     * @return bool
     */
    public function isUnlimited()
    {
        return $this->unlimited;
    }

    /**
     * @param bool $unlimited
     */
    public function setUnlimited($unlimited)
    {
        $this->unlimited = $unlimited;
    }

    /**
     * @return bool
     */
    public function isFirstSubscriptionPeriodIsFreeMultiple()
    {
        return $this->firstSubscriptionPeriodIsFreeMultiple;
    }

    /**
     * @param bool $firstSubscriptionPeriodIsFreeMultiple
     */
    public function setFirstSubscriptionPeriodIsFreeMultiple($firstSubscriptionPeriodIsFreeMultiple)
    {
        $this->firstSubscriptionPeriodIsFreeMultiple = $firstSubscriptionPeriodIsFreeMultiple;
    }

    /**
     * @return bool
     */
    public function isFirstSubscriptionPeriodIsFree()
    {
        return $this->firstSubscriptionPeriodIsFree;
    }

    /**
     * @param bool $firstSubscriptionPeriodIsFree
     */
    public function setFirstSubscriptionPeriodIsFree($firstSubscriptionPeriodIsFree)
    {
        $this->firstSubscriptionPeriodIsFree = $firstSubscriptionPeriodIsFree;
    }

    /**
     * @return bool
     */
    public function isAllowBonusCredit()
    {
        return $this->allowBonusCredit;
    }

    /**
     * @param bool $allowBonusCredit
     */
    public function setAllowBonusCredit($allowBonusCredit)
    {
        $this->allowBonusCredit = $allowBonusCredit;
    }

    /**
     * @return bool
     */
    public function isAllowBonusCreditMultiple()
    {
        return $this->allowBonusCreditMultiple;
    }

    /**
     * @param bool $allowBonusCreditMultiple
     */
    public function setAllowBonusCreditMultiple($allowBonusCreditMultiple)
    {
        $this->allowBonusCreditMultiple = $allowBonusCreditMultiple;
    }

    /**
     * @return int
     */
    public function getBonusCredit()
    {
        return $this->bonusCredit;
    }

    /**
     * @param bool $bonusCredit
     */
    public function setBonusCredit($bonusCredit)
    {
        $this->bonusCredit = $bonusCredit;
    }

    /**
     * @return bool
     */
    public function isProviderManagedSubscriptions()
    {
        return $this->providerManagedSubscriptions;
    }

    /**
     * @param bool $providerManagedSubscriptions
     */
    public function setProviderManagedSubscriptions($providerManagedSubscriptions)
    {
        $this->providerManagedSubscriptions = $providerManagedSubscriptions;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getFinalCurrency(): string
    {
        if (strlen($this->getDisplayCurrency()) > 0) {
            return $this->getDisplayCurrency();
        }
        return $this->getTierCurrency();
    }

    /**
     * @return int
     */
    public function getFinalCreditsForSubscription(): int
    {
        return $this->isUnlimited() ? 1000 : $this->getCredits();
    }

    /**
     * @return int
     */
    public function getFinalPeriodForSubscription(): int
    {
        return $this->getPeriodicity() == 8 ? $this->getCustomRenewPeriod() : $this->getPeriodicity();
    }

    /**
     * @return string
     */
    public function convertPeriod2Text(): string
    {
        switch ($this->getFinalPeriodForSubscription()){
            case self::DAILY:
                $periodicityText = 'day';
                break;
            case self::WEEKLY:
                $periodicityText = 'week';
                break;
            default:
                $periodicityText = 'day';
                break;
        }
        return $periodicityText;
    }

    /**
     * @return string
     */
    public function convertPeriodicity2Text(): string
    {
        switch ($this->getFinalPeriodForSubscription()){
            case self::DAILY:
                $periodicityText = 'daily';
                break;
            case self::WEEKLY:
                $periodicityText = 'weekly';
                break;
            default:
                $periodicityText = 'daily';
                break;
        }
        return $periodicityText;
    }

    /**
     * @return bool
     */
    public function isZeroCreditSubAvailable(): bool
    {
        return $this->zeroCreditSubAvailable;
    }

    /**
     * @param bool $zeroCreditSubAvailable
     */
    public function setZeroCreditSubAvailable(bool $zeroCreditSubAvailable): void
    {
        $this->zeroCreditSubAvailable = $zeroCreditSubAvailable;
    }
}