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
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;


    /**
     * SubscribeExternalAPICheck constructor.
     *
     * @param Client          $guzzleClient
     * @param string          $apiLink
     * @param LoggerInterface $logger
     */
    public function __construct(Client $guzzleClient, string $apiLink, LoggerInterface $logger)
    {
        $this->guzzleClient = $guzzleClient;
        $this->apiLink      = $apiLink;
        $this->logger = $logger;
    }

    public function checkIfExists(string $msisdn, int $carrierId): bool
    {
        if ((bool)strlen($this->apiLink)) {
            $result   = $this->guzzleClient->get(sprintf('%s/msisdn/%s/%s', $this->apiLink, $carrierId, $msisdn));
            $response = json_decode($result->getBody(), true);
            $isExists = $response['isExist'] ?? false;
            return (bool)$isExists;
        }
        return false;
    }

    public function registerSubscription(string $msisdn, int $carrierId): void
    {
        $this->logger->debug('apiLink',[
            $this->apiLink,
            $this
        ]);

        if((bool)strlen($this->apiLink)) {
            $this->guzzleClient->post(sprintf('%s/msisdn', $this->apiLink), $options = [
                RequestOptions::JSON => ['carrierId' => $carrierId, 'msisdn' => $msisdn],
            ]);
        }
    }

    public function deregisterSubscription(string $msisdn, int $carrierId): void
    {
        if((bool)strlen($this->apiLink)) {
            $this->guzzleClient->delete(sprintf('%s/msisdn', $this->apiLink), $options = [
                RequestOptions::JSON => ['carrierId' => $carrierId, 'msisdn' => $msisdn],
            ]);
        }
    }
}