<?php

namespace IdentificationBundle\Twig;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Identification\Service\PassthroughChecker;
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
     * IdentificationStatusExtension constructor.
     *
     * @param IdentificationDataStorage $dataStorage
     * @param SessionInterface          $session
     * @param CarrierRepository         $carrierRepository
     * @param PassthroughChecker        $passthroughChecker
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        SessionInterface $session,
        CarrierRepository $carrierRepository,
        PassthroughChecker $passthroughChecker
    )
    {
        $this->dataStorage        = $dataStorage;
        $this->session            = $session;
        $this->carrierRepository  = $carrierRepository;
        $this->passthroughChecker = $passthroughChecker;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('isCarrierDetected', [$this, 'isCarrierDetected']),

            new TwigFunction('getCarrierId', [$this, 'getCarrierId']),

            new TwigFunction('isIdentified', function () {
                $identificationData = $this->dataStorage->readIdentificationData();
                return isset($identificationData['identification_token']) && $identificationData['identification_token'];
            }),

            new TwigFunction('isConsentFlow', function () {
                $token = $this->dataStorage->readValue('consentFlow[token]');
                return (bool)$token;
            }),

            new TwigFunction('isWifiFlow', function () {
                return (bool)$this->dataStorage->readValue('is_wifi_flow');
            }),

            new TwigFunction('getIdentificationToken', function () {
                $identificationData = $this->dataStorage->readIdentificationData();
                return $identificationData['identification_token'] ?? null;
            }),

            new TwigFunction('isOtp', [$this, 'isOtp']),

            new TwigFunction('isCarrierPassthrough', [$this, 'isCarrierPassthrough']),

            new TwigFunction('isClickableSubImage', function () {
                return (bool)$this->dataStorage->readValue('is_clickable_sub_image');
            })
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
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        if (isset($ispDetectionData['carrier_id']) && $ispDetectionData['carrier_id']) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);
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