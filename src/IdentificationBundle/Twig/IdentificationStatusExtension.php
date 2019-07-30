<?php

namespace IdentificationBundle\Twig;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Identification\Service\PassthroughChecker;
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
     * @var PassthroughChecker
     */
    private $passthroughChecker;

    /**
     *
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        SessionInterface $session,
        CarrierRepository $carrierRepository,
        PassthroughChecker $passthroughChecker
    )
    {
        $this->dataStorage                   = $dataStorage;
        $this->session                       = $session;
        $this->carrierRepository             = $carrierRepository;
        $this->passthroughChecker            = $passthroughChecker;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
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

            new TwigFunction('isOtp', [$this, 'isOtp']),

            new TwigFunction('isCarrierPassthrough', [$this, 'isCarrierPassthrough']),

            new TwigFunction('isClickableSubImage', function () {
                // todo rework after task with landing page
                return false;
                //return (bool)$this->dataStorage->readValue('is_clickable_sub_image');
            })
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

    /**
     * @return bool
     */
    public function isCarrierPassthrough(): bool
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        try {
            $billingCarrierId = (int)$ispDetectionData['carrier_id'];
            $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            return $this->passthroughChecker->isCarrierPassthrough($carrier);
        } catch (\Throwable $e) {
            return false;
        }
    }
}