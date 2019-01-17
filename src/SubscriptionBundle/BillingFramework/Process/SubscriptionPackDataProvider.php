<?php

namespace SubscriptionBundle\BillingFramework\Process;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Country;
use App\Utils\UuidGenerator;
use PriceBundle\Entity\Strategy;
use PriceBundle\Entity\Tier;
use stdClass;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\Entity\Price;

/**
 * Class SubscriptionPackDataProvider to get data from billing framework
 */
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
     * BillingFrameworkHelperService constructor
     *
     * @param Client $billingFrameworkAPI
     */
    public function __construct(Client $billingFrameworkAPI)
    {
        $this->billingFrameworkAPI = $billingFrameworkAPI;
    }

    /**
     * @return array
     *
     * @throws BillingFrameworkException
     */
    public function getCarriers(): array
    {
        $errorMessage = sprintf("Error while trying to get carriers ");
        return $this->getDataFromAPI(self::DATA_METHOD_CARRIERS, Carrier::class, $errorMessage);
    }

    /**
     * @param Country $country
     *
     * @return Carrier[]
     *
     * @throws BillingFrameworkException
     */
    public function getCarriersForCountry(Country $country): array
    {
        $method = self::DATA_METHOD_CARRIERS . '/' . $country->getCountryCode();
        $errorMessage = sprintf("Error while trying to get carriers for country %s", $country->getCountryName());

        return $this->getDataFromAPI($method, Carrier::class, $errorMessage);
    }

    /***
     * @return Tier[]
     *
     * @throws BillingFrameworkException
     */
    public function getTiers(): array
    {
        return $this->getDataFromAPI(self::DATA_METHOD_TIERS, Tier::class, 'Error while fetching tiers');
    }

    /**
     * @param $carrierId
     *
     * @return Price[]
     *
     * @throws BillingFrameworkException
     */
    public function getTiersForCarrier($carrierId): array
    {
        $data = [];
        $method  = self::DATA_METHOD_TIERS_OF_CARRIER . $carrierId;
        $errorMessage = "Error while fetching tiers for carrier {$carrierId}";

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

    /**
     * @return Strategy[]
     *
     * @throws BillingFrameworkException
     */
    public function getBillingStrategies(): array
    {
        return $this->getDataFromAPI(
            self::DATA_METHOD_STRATEGIES,
            Strategy::class,
            'Error while fetching billing strategies'
        );
    }

    /**
     * @param $method
     * @param $responseObjectClass
     * @param $errorMessage
     *
     * @return array
     *
     * @throws BillingFrameworkException
     */
    private function getDataFromAPI($method, $responseObjectClass, $errorMessage)
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
                $responseObject = new $responseObjectClass(UuidGenerator::generate());
                $data[] = $this->mapDataToClass($responseObject, $response);
            }
        }

        return $data;
    }

    /**
     * @param $destination
     * @param stdClass $source
     *
     * @return mixed
     */
    private function mapDataToClass($destination, \stdClass $source)
    {
        $sourceReflection = new \ReflectionObject($source);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();

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