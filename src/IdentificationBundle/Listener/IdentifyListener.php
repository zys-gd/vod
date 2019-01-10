<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:52
 */

namespace IdentificationBundle\Listener;


use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use IdentificationBundle\Controller\ControllerWithIdentification;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Service\Action\Identification\Common\IdentificationDataExtractor;
use IdentificationBundle\Service\Action\Identification\Identifier;
use IdentificationBundle\Service\Carrier\ISPResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class IdentifyListener
{
    /**
     * @var ICountryCarrierDetection
     */
    private $carrierDetection;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var ISPResolver
     */
    private $ISPResolver;
    /**
     * @var Identifier
     */
    private $identifier;


    /**
     * IdentifyListener constructor.
     * @param ICountryCarrierDetection   $carrierDetection
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ISPResolver                $ISPResolver
     * @param Identifier                 $identifier
     */
    public function __construct(ICountryCarrierDetection $carrierDetection, CarrierRepositoryInterface $carrierRepository, ISPResolver $ISPResolver, Identifier $identifier)
    {
        $this->carrierDetection  = $carrierDetection;
        $this->carrierRepository = $carrierRepository;
        $this->ISPResolver       = $ISPResolver;
        $this->identifier        = $identifier;
    }

    public function onKernelController(FilterControllerEvent $event)
    {

        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0] ?? null;
        }

        if (!$controller || !($controller instanceof ControllerWithIdentification)) {
            return;
        }


        $request = $event->getRequest();
        $session = $request->getSession();


        $carrierISP = $this->detectISP($request, $session);

        if (IdentificationDataExtractor::extractFromSession($request->getSession())) {
            return;
        }

        $identificationData = [
            'identification_token' => md5(microtime(true)),
            'carrier_id'           => null,
        ];

        $carrierId = null;
        if ($carrierISP) {
            $carrierId = $this->resolveISP($carrierISP);
        }
        if ($carrierId) {
            $identificationData['carrier_id'] = $carrierId;
        }
        $session->set('identification_data', $identificationData);

        if (!$carrierId) {
            throw new \Exception('Hello wi-fi flow. Replace me by something?');
        }

        $result = $this->identifier->identify((int)$carrierId, $request);

        if ($response = $result->getOverridedResponse()) {
            $event->setController(function () use ($response) {
                return $response;
            });
        }
    }

    /**
     * @param $carrierISP
     * @return int|null
     */
    private function resolveISP(string $carrierISP): ?int
    {
        $carriers = $this->carrierRepository->findAllCarriers();
        foreach ($carriers as $carrier) {
            if ($this->ISPResolver->isISPMatches($carrierISP, $carrier)) {
                return $carrier->getBillingCarrierId();
                break;
            }
        }
        return null;
    }

    /**
     * @param $request
     * @param $session
     * @return string
     */
    private function detectISP(Request $request, SessionInterface $session): string
    {
        $ipAddress = $request->getClientIp();
        $ipAddress = '119.160.116.250';

        if ($session->has('isp_detection_data')) {
            $carrierISP       = $this->carrierDetection->getCarrier($ipAddress);
            $ispDetectionData = [
                'isp_name' => $carrierISP,
            ];
            $session->set('isp_detection_data', $ispDetectionData);
        } else {
            $ispDetectionData = $session->get('isp_detection_data');
            $carrierISP       = $ispDetectionData['carrier_isp'];
        }
        return $carrierISP;
    }
}