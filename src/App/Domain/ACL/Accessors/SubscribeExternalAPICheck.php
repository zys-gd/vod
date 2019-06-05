<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.05.19
 * Time: 17:45
 */

namespace App\Domain\ACL\Accessors;


use App\Domain\Service\CrossSubscriptionAPI\ApiConnector;

class SubscribeExternalAPICheck
{
    /**
     * @var ApiConnector
     */
    private $endpoint;


    /**
     * SubscribeExternalAPICheck constructor.
     */
    public function __construct(ApiConnector $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function checkOnExternalAPI(string $msisdn, int $carrierId): bool
    {
        try {

            return $this->endpoint->checkIfExists($msisdn, $carrierId);
        } catch (\Exception $exception) {
            return false;
        }
    }
}