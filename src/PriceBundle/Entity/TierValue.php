<?php

namespace PriceBundle\Entity;


use App\Domain\Entity\Carrier;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * Class TierValue. Represents a tier value for a carrier and a billing agregator
 */
class TierValue implements FbTierValueInterface, HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var integer
     */
    protected $bfProcessId;

    /**
     * @var Carrier
     */
    protected $carrier;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var string
     */
    protected $currency;

    /**
     * The parent Tier
     *
     * @var Tier
     */
    protected $tier;

    /**
     * @var strategy
     */
    protected $strategy;

    /**
     * @var string
     */
    protected $description;

    /**
     * TierValue constructor
     *
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
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Sets the Billing Framework process id
     * @param integer $bfProcessId
     */
    public function setBfProcessId($bfProcessId)
    {
        $this->bfProcessId = $bfProcessId;
    }

    /**
     * Returns the Webshop process id
     * @return integer
     */
    public function getBfProductId()
    {
        return $this->uuid;
    }

    /**
     * Returns the Billing Framework process id
     * @return integer
     */
    public function getBfProcessId()
    {
        return $this->bfProcessId;
    }

    /**
     * Returns the parent Tier
     * @return Tier | null
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * Returns the parent Tier id
     * @return integer
     */
    public function getTierId()
    {
        return $this->tier->getUuid();
    }

    /**
     * Returns the parent Tier id
     * @return integer
     */
    public function getBfTierId()
    {
        return $this->tier->getBfTierId();
    }

    /**
     * Sets the parent Tier
     * @param Tier $tier
     * @return TierValue
     */
    public function setTier(Tier $tier)
    {
        $this->tier = $tier;
        return $this;
    }

    /**
     * Returns the parent Strategy
     * @return Strategy | null
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Returns the parent Strategy id
     * @return integer
     */
    public function getStrategyId()
    {
        return $this->strategy->getUuid();
    }

    /**
     * Returns the entity id
     * @return int | null
     */
    public function getBfStrategyId()
    {
        return $this->strategy->getBfStrategyId();
    }

    /**
     * Sets the date expired
     * @param FbTierValueInterface $tierValue
     */
    public function bfPrepare(FbTierValueInterface $tierValue)
    {
        $this->bfTierId = $tierValue->getBfTierId();
        $this->bfStrategyId = $tierValue->getBfStrategyId();
    }

    /**
     * Sets the parent Strategy
     * @param Strategy $strategy
     */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * returns the carrier id
     * @return Carrier | null
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * Sets the Carrier
     *
     * @param Carrier $carrier
     */
    public function setCarrier(Carrier $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * Returns the value of the tier price
     * @return float | null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value of the tier
     * @param float $value
     */
    public function setValue(float $value)
    {
        $this->value = $value;
    }

    /**
     * returns the ISO 4217 code for the currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets the ISO 4217 code for the currency
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Check if Tier Value is active
     * $return bool
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->tier->getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}