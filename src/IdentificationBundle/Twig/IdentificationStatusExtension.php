<?php

namespace IdentificationBundle\Twig;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
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
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * IdentificationStatusExtension constructor.
     *
     * @param IdentificationDataStorage $dataStorage
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param SessionInterface $session
     * @param CarrierRepository $carrierRepository
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        SessionInterface $session,
        CarrierRepository $carrierRepository
    ) {
        $this->dataStorage = $dataStorage;
        $this->session = $session;
        $this->carrierRepository = $carrierRepository;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('isCarrierDetected', function () {
                return (bool) IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
            }),

            new TwigFunction('getCarrierId', function () {
                return IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
            }),

            new TwigFunction('isIdentified', function () {
                return (bool) $this->dataStorage->getIdentificationToken();
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

            new TwigFunction('isOtp', [$this, 'isOtp'])
        ];
    }

    /**
     * @return bool
     */
    public function isOtp(): bool
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if ($billingCarrierId) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);

            return $carrier->isConfirmationClick();
        }

        return false;
    }
}