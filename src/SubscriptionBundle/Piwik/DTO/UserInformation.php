<?php


namespace SubscriptionBundle\Piwik\DTO;

class UserInformation
{

    /**
     * @var string
     */
    private $country;
    /**
     * @var string
     */
    private $ip;
    /**
     * @var string
     */
    private $connection;
    /**
     * @var string
     */
    private $msisdn;
    /**
     * @var int
     */
    private $operator;
    /**
     * @var int
     */
    private $provider;
    /**
     * @var int
     */
    private $deviceHeight;
    /**
     * @var int
     */
    private $deviceWidth;
    /**
     * @var string
     */
    private $affiliate;

    public function __construct(
        string $country,
        string $ip,
        string $connection,
        string $msisdn,
        int $operator,
        int $provider,
        int $deviceHeight,
        int $deviceWidth,
        string $affiliate = null
    )
    {
        $this->country      = $country;
        $this->ip           = $ip;
        $this->connection   = $connection;
        $this->msisdn       = $msisdn;
        $this->operator     = $operator;
        $this->provider     = $provider;
        $this->deviceHeight = $deviceHeight;
        $this->deviceWidth  = $deviceWidth;
        $this->affiliate    = $affiliate;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getMsisdn(): string
    {
        return $this->msisdn;
    }

    /**
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * @return int
     */
    public function getProvider(): int
    {
        return $this->provider;
    }

    /**
     * @return int
     */
    public function getDeviceHeight(): int
    {
        return $this->deviceHeight;
    }

    /**
     * @return int
     */
    public function getDeviceWidth(): int
    {
        return $this->deviceWidth;
    }

    /**
     * @return string
     */
    public function getAffiliate(): ?string
    {
        return $this->affiliate;
    }



}