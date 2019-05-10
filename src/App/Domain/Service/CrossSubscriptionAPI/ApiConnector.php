<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.05.19
 * Time: 17:49
 */

namespace App\Domain\Service\CrossSubscriptionAPI;


use GuzzleHttp\Client;

class ApiConnector
{

    /**
     * @var Client
     */
    private $guzzleClient;
    /**
     * @var string
     */
    private $apiLink;


    /**
     * SubscribeExternalAPICheck constructor.
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient, string $apiLink)
    {
        $this->guzzleClient = $guzzleClient;
        $this->apiLink      = $apiLink;
    }

    public function checkIfExists(string $msisdn, int $carrierId): bool
    {

        $result   = $this->guzzleClient->get(sprintf('%s/msisdn/%s', $this->apiLink, $msisdn));
        $response = json_decode($result->getBody());
        $isExists = $response['isExists'] ?? false;
        return (bool)$isExists;


    }
}