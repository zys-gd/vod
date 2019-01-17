<?php
/**
 * Created by PhpStorm.
 * User: Vlad
 * Date: 20.12.2016
 * Time: 22:22
 */

namespace LegacyBundle\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

class ExchangeRate implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $currencyName;

    /**
     * @var decimal
     */
    private $exchangeRate;

    /**
     * ExchangeRate constructor.
     * @throws \Exception
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
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    /**
     * @param string $currencyName
     */
    public function setCurrencyName($currencyName)
    {
        $this->currencyName = $currencyName;
    }

    /**
     * @return decimal
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param decimal $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }


}