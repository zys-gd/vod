<?php

namespace App\Domain\Service;

use App\Domain\Constants\ConstBillingCarrierId;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CarrierOTPVerifier
 */
class CarrierOTPVerifier
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;

    /**
     * TODO: do with interface of carrier's implementation subscriber?
     * @var array
     */
    private $otpCarriers = [
        ConstBillingCarrierId::MOBILINK_PAKISTAN
    ];

    /**
     * CarrierOTPVerifier constructor
     *
     * @param CarrierRepository $carrierRepository
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
    }

    /**
     * Forced WiFi flow for marked carriers
     *
     * @param SessionInterface $session
     */
    public function forceWifi(SessionInterface $session)
    {
        if (in_array(IdentificationFlowDataExtractor::extractBillingCarrierId($session) ?? '', $this->otpCarriers)) {
            $this->wifiIdentificationDataStorage->setWifiFlow(true);
        }
    }
}