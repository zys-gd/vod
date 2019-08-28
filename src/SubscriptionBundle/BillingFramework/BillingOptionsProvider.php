<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.07.19
 * Time: 17:13
 */

namespace SubscriptionBundle\BillingFramework;


class BillingOptionsProvider
{
    /**
     * @var string
     */
    private $apiHost;
    /**
     * @var string
     */
    private $clientId;


    /**
     * BillingOptionsProvider constructor.
     * @param string $apiHost
     * @param string $clientId
     */
    public function __construct(string $apiHost, string $clientId)
    {
        $this->apiHost  = $apiHost;
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getApiHost(): string
    {
        return $this->apiHost;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }


}