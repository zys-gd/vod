<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 10/08/17
 * Time: 1:06 PM
 */

namespace SubscriptionBundle\BillingFramework\Process;


use AppBundle\Entity\Carrier;
use AppBundle\Entity\Country;
use PriceBundle\Entity\Strategy;
use PriceBundle\Entity\Tier;
use stdClass;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\Entity\Price;
use SubscriptionBundle\BillingFramework\Process\API\Client;


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

    /***
     * @return Tier[]
     * @throws \SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException
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
                $oPrice = new Price();
                $oPrice->setUuid($responsePricepoint->id);
                $oPrice->setPricepoint($responsePricepoint->pricepoint);
                $oPrice->setPricepointName($responsePricepoint->pricepoint_name);
                $oPrice->setValue($responsePricepoint->value);
                $oPrice->setTierId($responsePricepoint->tier);
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
     * @throws \SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException
     */
    public function getBillingStrategies(): array
    {
        $method       = self::DATA_METHOD_STRATEGIES;
        $errorMessage = "Error while fetching billing strategies.";
        return $this->getDataFromAPI($method, Strategy::class, $errorMessage);
    }


}