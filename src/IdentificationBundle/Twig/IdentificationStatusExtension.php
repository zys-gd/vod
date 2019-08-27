<?php

namespace IdentificationBundle\Twig;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class IdentificationStatusExtension
 */
class IdentificationStatusExtension extends AbstractExtension
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * IdentificationStatusExtension constructor.
     *
     * @param IdentificationDataStorage  $dataStorage
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param SessionInterface           $session
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        SessionInterface $session,
        CarrierRepositoryInterface $carrierRepository
    )
    {
        $this->dataStorage       = $dataStorage;
        $this->session           = $session;
        $this->carrierRepository = $carrierRepository;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
        $this->session                       = $session;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('isCarrierDetected', function () {
                return (bool)IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
            }),

            new TwigFunction('getCarrierId', function () {
                return IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
            }),

            new TwigFunction('isIdentified', function () {
                return (bool)$this->dataStorage->getIdentificationToken();
            }),

            new TwigFunction('isConsentFlow', function () {
                $token = $this->dataStorage->readValue(IdentificationDataStorage::CONSENT_FLOW_TOKEN_KEY);
                return (bool)$token;
            }),

            new TwigFunction('isWifiFlow', function () {
                return (bool)$this->wifiIdentificationDataStorage->isWifiFlow();
            }),

            new TwigFunction('getIdentificationToken', function () {
                return $this->dataStorage->getIdentificationToken();
            }),

        ];
    }

    /**
     * @return bool
     */
    public function isCarrierDetected(): bool
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        return isset($ispDetectionData['carrier_id']) && $ispDetectionData['carrier_id'];
    }

    /**
     * @return int|null
     */
    public function getCarrierId(): ?int
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        return empty($ispDetectionData['carrier_id']) ? null : (int)$ispDetectionData['carrier_id'];
    }

    /**
     * @return bool
     */
    public function isOtp(): bool
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if ($billingCarrierId) {
            /** @var CarrierInterface $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            return $carrier->isConfirmationClick();
        }

        return false;
    }
}