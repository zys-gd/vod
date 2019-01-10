<?php

namespace SubscriptionBundle\Entity;


class Price
{
    /** @var string */
    private $uuid;

    /** @var  integer */
    private $pricepoint;

    /** @var  string */
    private $pricepointName;

    /** @var  float */
    private $value;

    /** @var  integer */
    private $tierId;

    /** @var  boolean  */
    private $byCarrier;

    /** @var float  */
    private $priceWithTax = 0;

    /**
     * @var string
     */
    private $currency;

    /**
     * Price constructor.
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
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }


    /**
     * @return int
     */
    public function getPricepoint(): int
    {
        return $this->pricepoint;
    }

    /**
     * @param int $pricepoint
     */
    public function setPricepoint(int $pricepoint)
    {
        $this->pricepoint = $pricepoint;
    }

    /**
     * @return string
     */
    public function getPricepointName(): string
    {
        return $this->pricepointName;
    }

    /**
     * @param string $pricepointName
     */
    public function setPricepointName(string $pricepointName)
    {
        $this->pricepointName = $pricepointName;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value)
    {
        $this->value = $value;
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
     */
    public function setTierId($tierId)
    {
        $this->tierId = $tierId;
    }

    /**
     * @return bool
     */
    public function isByCarrier(): bool
    {
        return $this->byCarrier;
    }

    /**
     * @param bool $byCarrier
     */
    public function setByCarrier(bool $byCarrier)
    {
        $this->byCarrier = $byCarrier;
    }

    public function getName()
    {
        $price_text = $this->getPriceWithTax() > 0 ? $this->getPriceWithTax() : $this->getValue();
        return round($price_text,2, PHP_ROUND_HALF_UP) . " ".$this->getCurrency();
    }

    public function __toString()
    {
      return $this->getName();
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getPriceWithTax(): float
    {
        return $this->priceWithTax;
    }

    /**
     * @param int $priceWithTax
     */
    public function setPriceWithTax(float $priceWithTax)
    {
        $this->priceWithTax = $priceWithTax;
    }
}