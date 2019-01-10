<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 19:51
 */

namespace IdentificationBundle\BillingFramework\Data;


use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;

class DataProvider
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var ProcessResponseMapper
     */
    private $mapper;

    /**
     * DataProvider constructor.
     * @param Client $client
     */
    public function __construct(Client $client, ProcessResponseMapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    public function getProcessData(string $processId): ProcessResult
    {
        $response = $this->client->sendGetDataRequestWithoutCache('process', $processId);

        return $this->mapper->map($response->type, (object)['data' => $response]);
    }


    /**
     * @param $method
     * @param $responseObjectClass
     * @return array
     * @throws BillingFrameworkException
     * @throws \SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException
     */
    public function getDataFromAPI($method, $responseObjectClass): array
    {
        $data = [];

        $responseObjectArray = $this->client->sendGetDataRequest($method);

        if ($responseObjectArray) {
            foreach ($responseObjectArray as $response) {
                $responseObject = new $responseObjectClass;
                $data[]         = $this->mapDataToClass($responseObject, $response);
            }
        }
        return $data;
    }

    private function mapDataToClass($destination, \stdClass $source)
    {
        $sourceReflection      = new \ReflectionObject($source);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties      = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name  = $sourceProperty->getName();
            $value = $sourceProperty->getValue($source);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
}