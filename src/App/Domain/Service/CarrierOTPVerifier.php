<?php


namespace App\Domain\Service;


use App\Domain\Constants\ConstBillingCarrierId;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CarrierOTPVerifier
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;

    /**
     * TODO: do with interface of carrier's implementation subscriber?
     * @var array
     */
    private $otpCarriers = [
        ConstBillingCarrierId::MOBILINK_PAKISTAN
    ];

    /**
     * CarrierOTPVerifier constructor.
     *
     * @param CarrierRepository         $carrierRepository
     * @param IdentificationDataStorage $dataStorage
     */
    public function __construct(CarrierRepository $carrierRepository, IdentificationDataStorage $dataStorage)
    {
        $this->carrierRepository = $carrierRepository;
        $this->dataStorage = $dataStorage;
    }

    /**
     * Forced WiFi flow for marked carriers
     *
     * @param SessionInterface $session
     */
    public function forceWifi(SessionInterface $session)
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($session);

        in_array($ispDetectionData['carrier_id'] ?? '', $this->otpCarriers)
        && $this->dataStorage->storeValue('is_wifi_flow', true);
    }
}