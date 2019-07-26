<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 10/08/17
 * Time: 1:06 PM
 */

namespace SubscriptionBundle\BillingFramework\Process;


use ExtrasBundle\Utils\UuidGenerator;
use stdClass;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\SubscriptionPack\DTO\Strategy;
use SubscriptionBundle\SubscriptionPack\DTO\Tier;
use SubscriptionBundle\Entity\Price;


class SubscriptionPackDataProvider
{
    const DATA_METHOD_CARRIERS = "carriers";
    const DATA_METHOD_TIERS = "tiers/";
    const DATA_METHOD_TIERS_OF_CARRIER = "carrier/";
    const DATA_METHOD_STRATEGIES = "strategies/";
    /**
     * @var Client
     */
    private $billingFrameworkAPI;

    /**
     * BillingFrameworkHelperService constructor.
     * @param \SubscriptionBundle\BillingFramework\Process\API\Client $billingFrameworkAPI
     */
    public function __construct(Client $billingFrameworkAPI)
    {
        $this->billingFrameworkAPI = $billingFrameworkAPI;
    }


    public function getCarriers(): array
    {
        $errorMessage = sprintf("Error while trying to get carriers ");
        return $this->getDataFromAPI(self::DATA_METHOD_CARRIERS, Carrier::class, $errorMessage);
    }

    /**
     * @param $method
     * @param $responseObjectClass
     * @param $errorMessage
     * @return array
     * @throws \SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException
     */
    private function getDataFromAPI($method, $responseObjectClass, $errorMessage): array
    {
        $data = [];

        /** @var stdClass[] $strategiesResponse */
        try {
            $responseObjectArray = $this->billingFrameworkAPI->sendGetDataRequest($method);
        } catch (BillingFrameworkException $ex) {
            throw new BillingFrameworkException($errorMessage, $ex->getCode(), $ex);
        }

        if ($responseObjectArray) {
            foreach ($responseObjectArray as $response) {
                $responseObject = new $responseObjectClass();
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

    /**
     * @param Country $country
     * @return Carrier[]
     * @throws BillingFrameworkException
     */
    public function getCarriersForCountry(Country $country): array
    {

        $method       = self::DATA_METHOD_CARRIERS . "/" . $country->getCountryCode();
        $errorMessage = sprintf("Error while trying to get carriers for country %s", $country->getCountryName());
        return $this->getDataFromAPI($method, Carrier::class, $errorMessage);
    }

    /**
     * @return Strategy[]
     * @throws \SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException
     */
    public function getBillingStrategies(): array
    {
        $method       = self::DATA_METHOD_STRATEGIES;
        $errorMessage = "Error while fetching billing strategies.";
        return $this->getDataFromAPI($method, Strategy::class, $errorMessage);
    }

    /***
     * @return Tier[]
     * @throws BillingFrameworkException
     */
    public function getTiers(): array
    {
        $errorMessage = "Error while fetching tiers.";
        return $this->getDataFromAPI(self::DATA_METHOD_TIERS, Tier::class, $errorMessage);
    }

    /**
     * @param $carrierId
     * @return Price[]
     * @throws BillingFrameworkException
     */
    public function getTiersForCarrier($carrierId): array
    {
        $data         = [];
        $method       = self::DATA_METHOD_TIERS_OF_CARRIER . $carrierId;
        $errorMessage = "Error while fetching tiers for carrier {$carrierId}.";

        try {
            $responseObject = $this->billingFrameworkAPI->sendGetDataRequest($method);
        } catch (BillingFrameworkException $ex) {
            throw new BillingFrameworkException($errorMessage, $ex->getCode(), $ex);
        }


        if (isset($responseObject) && !empty($responseObject->prices) && is_array($responseObject->prices)) {
            foreach ($responseObject->prices as $responsePricepoint) {
                $oPrice = new Price(UuidGenerator::generate());
                $oPrice->setBfPriceId($responsePricepoint->id);
                $oPrice->setPricepoint($responsePricepoint->pricepoint);
                $oPrice->setPricepointName($responsePricepoint->pricepoint_name);
                $oPrice->setValue($responsePricepoint->value);
                $oPrice->setBfTierId($responsePricepoint->tier);
                $oPrice->setByCarrier($responsePricepoint->by_carrier);
                $oPrice->setCurrency($responseObject->currency);
                $oPrice->setPriceWithTax($responsePricepoint->price_with_tax ?? 0);
                $data[] = $oPrice;
            }
        }

        return $data;
    }

}