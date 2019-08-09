<?php

namespace IdentificationBundle\Twig;

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
     * IdentificationStatusExtension constructor.
     *
     * @param IdentificationDataStorage     $dataStorage
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param SessionInterface              $session
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        SessionInterface $session
    )
    {

        $this->dataStorage                   = $dataStorage;
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


}