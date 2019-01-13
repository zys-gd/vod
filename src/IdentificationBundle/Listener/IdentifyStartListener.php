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
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Identifier;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Identification\Service\ISPResolver;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class IdentifyStartListener
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
     * @var TokenGenerator
     */
    private $generator;
    /**
     * @var RouteProvider
     */
    private $routeProvider;


    /**
     * IdentifyStartListener constructor.
     * @param ICountryCarrierDetection                                   $carrierDetection
     * @param CarrierRepositoryInterface                                 $carrierRepository
     * @param \IdentificationBundle\Identification\Service\ISPResolver   $ISPResolver
     * @param Identifier                                                 $identifier
     * @param TokenGenerator                                             $generator
     * @param \IdentificationBundle\Identification\Service\RouteProvider $routeProvider
     */
    public function __construct(
        ICountryCarrierDetection $carrierDetection,
        CarrierRepositoryInterface $carrierRepository,
        ISPResolver $ISPResolver,
        Identifier $identifier,
        TokenGenerator $generator,
        RouteProvider $routeProvider
    )
    {
        $this->carrierDetection  = $carrierDetection;
        $this->carrierRepository = $carrierRepository;
        $this->ISPResolver       = $ISPResolver;
        $this->identifier        = $identifier;
        $this->generator         = $generator;
        $this->routeProvider     = $routeProvider;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0] ?? null;
        }
        if (!$controller) {
            return;
        }

        if (!($controller instanceof ControllerWithISPDetection)) {
            return;
        }
        $session   = $request->getSession();
        $ipAddress = $request->getClientIp();
        $carrierId = $this->detectCarrier($ipAddress, $session);
        if (!$carrierId) {
            $event->setController(function () use ($session) {
                return $this->startWifiFlow($session);
            });
            return;
        }

        if (!($controller instanceof ControllerWithIdentification)) {
            return;
        }

        $response = $this->doIdentify($request, $carrierId);
        if ($response) {
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
    private function detectCarrier(string $ipAddress, SessionInterface $session): ?int
    {

        if (!$session->has('isp_detection_data')) {
            $carrierISP = $this->carrierDetection->getCarrier($ipAddress);
            $carrierId  = null;
            if ($carrierISP) {
                $carrierId = $this->resolveISP($carrierISP);
            }
            if ($carrierId) {
                $ispDetectionData = [
                    'carrier_id' => $carrierId,
                ];
                $session->set('isp_detection_data', $ispDetectionData);
            }
        } else {
            $ispDetectionData = $session->get('isp_detection_data');
            $carrierId        = $ispDetectionData['carrier_id'];
        }

        return $carrierId;
    }

    private function startWifiFlow(SessionInterface $session): Response
    {
        $session->set('is_wifi_flow', true);

        return new RedirectResponse($this->routeProvider->getLinkToWifiFlowPage());

    }

    /**
     * @param $request
     * @param $carrierId
     * @param $session
     * @return null|Response
     */
    private function doIdentify(Request $request, int $carrierId): ?Response
    {
        $session = $request->getSession();
        if (IdentificationFlowDataExtractor::extractIdentificationData($session)) {
            return null;
        }

        $response = null;
        try {
            $token    = $this->generator->generateToken();
            $result   = $this->identifier->identify((int)$carrierId, $request, $token, $request->getSession());
            $response = $result->getOverridedResponse();

        } catch (FailedIdentificationException $exception) {
            $response = $this->startWifiFlow($request->getSession());
        }

        return $response;
    }
}