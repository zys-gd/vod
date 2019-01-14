<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 13:25
 */

namespace IdentificationBundle\BillingFramework\Process\DTO;


class PinRequestResult
{
    /**
     * @var string
     */
    private $userIdentifier;
    /**
     * @var bool
     */
    private $needVerifyRequest;
    /**
     * @var array
     */
    private $rawData;


    /**
     * PinRequestResult constructor.
     * @param string $userIdentifier
     * @param bool   $needVerifyRequest
     * @param array  $rawData
     */
    public function __construct(string $userIdentifier, bool $needVerifyRequest, array $rawData)
    {
        $this->userIdentifier    = $userIdentifier;
        $this->needVerifyRequest = $needVerifyRequest;
        $this->rawData           = $rawData;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return bool
     */
    public function isNeedVerifyRequest(): bool
    {
        return $this->needVerifyRequest;
    }

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }


}