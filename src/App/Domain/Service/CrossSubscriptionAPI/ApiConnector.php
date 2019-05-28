<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.05.19
 * Time: 17:49
 */

namespace App\Domain\Service\CrossSubscriptionAPI;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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
        $response = json_decode($result->getBody(), true);
        $isExists = $response['isExist'] ?? false;
        return (bool)$isExists;
    }

    public function registerSubscription(string $msisdn, int $carrierId): void
    {
        $this->guzzleClient->post(sprintf('%s/msisdn', $this->apiLink), $options = [
            RequestOptions::JSON => ['carrierId' => $carrierId, 'msisdn' => $msisdn],
        ]);
    }
}