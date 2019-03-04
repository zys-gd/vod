<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 13:51
 */

namespace IdentificationBundle\Twig;


use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IdentificationStatusExtension extends \Twig_Extension
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
     * IdentificationStatusExtension constructor.
     *
     * @param IdentificationDataStorage $dataStorage
     * @param SessionInterface          $session
     * @param CarrierRepository         $carrierRepository
     */
    public function __construct(IdentificationDataStorage $dataStorage,
        SessionInterface $session,
        CarrierRepository $carrierRepository)
    {
        $this->dataStorage = $dataStorage;
        $this->session = $session;
        $this->carrierRepository = $carrierRepository;
    }

    public function getFunctions()
    {
        return [

            new \Twig_SimpleFunction('isCarrierDetected', [$this, 'isCarrierDetected']),

            new \Twig_SimpleFunction('isIdentified', function () {
                $identificationData = $this->dataStorage->readIdentificationData();
                return isset($identificationData['identification_token']) && $identificationData['identification_token'];
            }),
            new \Twig_SimpleFunction('isConsentFlow', function () {
                $token = $this->dataStorage->readValue('consentFlow[token]');
                return (bool)$token;
            }),

            new \Twig_SimpleFunction('isWifiFlow', function () {
                return (bool)$this->dataStorage->readValue('is_wifi_flow');
            }),

            new \Twig_SimpleFunction('getIdentificationToken', function () {
                $identificationData = $this->dataStorage->readIdentificationData();
                return $identificationData['identification_token'] ?? null;
            }),

            new \Twig_SimpleFunction('isOtp', [$this, 'isOtp']),
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
     * @return bool
     */
    public function isOtp(): bool
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        if (isset($ispDetectionData['carrier_id']) && $ispDetectionData['carrier_id']) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);
            return $carrier->isLpOtp();
        }
        return false;
    }
}