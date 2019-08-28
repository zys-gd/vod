<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.07.19
 * Time: 14:15
 */

namespace IdentificationBundle\WifiIdentification;


use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider;

class WifiPhoneOptionsProvider
{
    /**
     * @var WifiIdentificationHandlerProvider
     */
    private $provider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;


    /**
     * WifiPhoneOptionsProvider constructor.
     * @param WifiIdentificationHandlerProvider $provider
     */
    public function __construct(WifiIdentificationHandlerProvider $provider, CarrierRepositoryInterface $carrierRepository)
    {
        $this->provider          = $provider;
        $this->carrierRepository = $carrierRepository;
    }

    public function getPhoneValidationOptions(int $billingCarrierId): PhoneValidationOptions
    {
        $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);

        $handler = $this->provider->get($carrier);

        return $handler->getPhoneValidationOptions();
    }
}