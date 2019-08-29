<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.05.18
 * Time: 16:54
 */

namespace SubscriptionBundle\Affiliate\Service;


use GuzzleHttp\Client;

class GuzzleClientFactory
{
    public function getClient(array $config = []): Client
    {

        $mergedConfig = array_merge(['connect_timeout' => 60, 'read_timeout' => 60, 'timeout' => 60], $config);

        return new Client($mergedConfig);

    }
}