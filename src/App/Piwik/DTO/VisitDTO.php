<?php


namespace App\Piwik\DTO;

class VisitDTO
{
    /**
     * @var string|null
     */
    private $msisdn;
    /**
     * billingCarrierId
     * @var string|null
     */
    private $operator;
    /**
     * @var string|null
     */
    private $affiliate;
    /**
     * @var string|null
     */
    private $country;
    /**
     * @var string|null
     */
    private $ip;

    /**
     * @var string|null
     */
    private $connection;

    public function __construct(
        string $country = null,
        string $ip = null,
        string $connection = null,
        string $msisdn = null,
        string $operator = null,
        string $affiliate = null
    )
    {
        $this->msisdn     = $msisdn;
        $this->operator   = $operator;
        $this->affiliate  = $affiliate;
        $this->country    = $country;
        $this->ip         = $ip;
        $this->connection = $connection;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @return string|null
     */
    public function getMsisdn(): ?string
    {
        return $this->msisdn;
    }

    /**
     * @return string|null
     */
    public function getOperator(): ?string
    {
        return $this->operator;
    }

    /**
     * @return string|null
     */
    public function getAffiliate(): ?string
    {
        return $this->affiliate;
    }

    /**
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return $this->connection;
    }
}