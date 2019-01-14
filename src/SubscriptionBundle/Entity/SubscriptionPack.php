<?php

namespace SubscriptionBundle\Entity;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Country;
use Doctrine\Common\Collections\ArrayCollection;
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
        'DAILY' => self::DAILY,
        'WEEKLY' => self::WEEKLY,
        'MONTHLY' => self::MONTHLY,
        'CUSTOM_PERIODICITY' => self::CUSTOM_PERIODICITY,
    ];

    const ACTIVE_SUBSCRIPTION_PACK = 1;
    const INACTIVE_SUBSCRIPTION_PACK = 0;
    const STATUSES = [
        'ACTIVE' => self::ACTIVE_SUBSCRIPTION_PACK,
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
     * @var Carrier
     */
    private $carrier;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $displayCurrency = '';

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
    private $firstSubscriptionPeriodIsFree = false;

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

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
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
     * @return Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param Carrier $carrier
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
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
     * @param $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
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
     * @return string
     */
    public function getBuyStrategy()
    {
        return $this->buyStrategy;
    }

    /**
     * @param string $buyStrategy
     */
    public function setBuyStrategy($buyStrategy)
    {
        $this->buyStrategy = $buyStrategy;
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
     * @return string
     */
    public function getRenewStrategy()
    {
        return $this->renewStrategy;
    }

    /**
     * @param string $renewStrategy
     */
    public function setRenewStrategy($renewStrategy)
    {
        $this->renewStrategy = $renewStrategy;
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
}