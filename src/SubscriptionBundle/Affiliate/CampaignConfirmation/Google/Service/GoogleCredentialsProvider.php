<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 18:18
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Service;


class GoogleCredentialsProvider
{
    /**
     * @var string
     */
    private $clientId;
    /**
     * @var string
     */
    private $clientSecret;
    /**
     * @var string
     */
    private $refreshToken;
    private $developerToken;
    private $clientCustomerId;


    /**
     * GoogleCredentialsProvider constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $refreshToken
     * @param string $developerToken
     * @param string $clientCustomerId
     */
    public function __construct(string $clientId, string $clientSecret, string $refreshToken, string $developerToken, string $clientCustomerId)
    {
        $this->clientId         = $clientId;
        $this->clientSecret     = $clientSecret;
        $this->refreshToken     = $refreshToken;
        $this->developerToken   = $developerToken;
        $this->clientCustomerId = $clientCustomerId;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return string
     */
    public function getDeveloperToken(): string
    {
        return $this->developerToken;
    }

    /**
     * @return string
     */
    public function getClientCustomerId(): string
    {
        return $this->clientCustomerId;
    }


}