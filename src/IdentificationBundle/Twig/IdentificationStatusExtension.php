<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 13:51
 */

namespace IdentificationBundle\Twig;


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
     * IdentificationStatusExtension constructor.
     * @param IdentificationDataStorage $dataStorage
     * @param SessionInterface          $session
     */
    public function __construct(IdentificationDataStorage $dataStorage, SessionInterface $session)
    {
        $this->dataStorage = $dataStorage;
        $this->session     = $session;
    }

    public function getFunctions()
    {
        return [

            new \Twig_SimpleFunction('isCarrierDetected', function () {
                $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
                return isset($ispDetectionData['carrier_id']) && $ispDetectionData['carrier_id'];
            }),

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
            })
        ];
    }

}