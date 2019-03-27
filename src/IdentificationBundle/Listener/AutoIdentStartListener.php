<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:52
 */

namespace IdentificationBundle\Listener;


use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use Doctrine\Common\Annotations\AnnotationReader;
use IdentificationBundle\Controller\Annotation\NoRedirectToWhoops;
use IdentificationBundle\Controller\ControllerWithIdentification;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Identifier;
use IdentificationBundle\Identification\Service\CarrierSelector;
use IdentificationBundle\Identification\Service\DeviceDataProvider;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\ISPResolver;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class AutoIdentStartListener
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
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var IdentificationStatus
     */
    private $identificationStatus;
    /**
     * @var AnnotationReader
     */
    private $annotationReader;
    /**
     * @var CarrierSelector
     */
    private $carrierSelector;
    /**
     * @var DeviceDataProvider
     */
    private $deviceDataProvider;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * AutoIdentStartListener constructor.
     * @param ICountryCarrierDetection                                   $carrierDetection
     * @param CarrierRepositoryInterface                                 $carrierRepository
     * @param \IdentificationBundle\Identification\Service\ISPResolver   $ISPResolver
     * @param Identifier                                                 $identifier
     * @param TokenGenerator                                             $generator
     * @param \IdentificationBundle\Identification\Service\RouteProvider $routeProvider
     * @param IdentificationDataStorage                                  $dataStorage
     * @param IdentificationStatus                                       $identificationStatus
     * @param AnnotationReader                                           $annotationReader
     * @param CarrierSelector                                            $carrierSelector
     * @param DeviceDataProvider                                         $deviceDataProvider
     * @param LoggerInterface                                            $logger
     */
    public function __construct(
        ICountryCarrierDetection $carrierDetection,
        CarrierRepositoryInterface $carrierRepository,
        ISPResolver $ISPResolver,
        Identifier $identifier,
        TokenGenerator $generator,
        RouteProvider $routeProvider,
        IdentificationDataStorage $dataStorage,
        IdentificationStatus $identificationStatus,
        AnnotationReader $annotationReader,
        CarrierSelector $carrierSelector,
        DeviceDataProvider $deviceDataProvider,
        LoggerInterface $logger
    )
    {
        $this->carrierDetection     = $carrierDetection;
        $this->carrierRepository    = $carrierRepository;
        $this->ISPResolver          = $ISPResolver;
        $this->identifier           = $identifier;
        $this->generator            = $generator;
        $this->routeProvider        = $routeProvider;
        $this->dataStorage          = $dataStorage;
        $this->identificationStatus = $identificationStatus;
        $this->annotationReader     = $annotationReader;
        $this->carrierSelector      = $carrierSelector;
        $this->deviceDataProvider   = $deviceDataProvider;
        $this->logger               = $logger;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $args = $event->getController();
        if (is_array($args)) {
            $controller = $args[0] ?? null;
            $method     = $args[1] ?? null;
        } else {
            $method = $controller = $args;
        }

        if (!$controller || !$method) {
            return;
        }

        if (!($controller instanceof ControllerWithISPDetection)) {
            return;
        }

        $session   = $request->getSession();
        $ipAddress = $request->getClientIp();
        $carrierId = $this->detectCarrier($ipAddress, $session);

        if (!$carrierId) {
            $response = $this->startWifiFlow($session);
            if ($this->isRedirectToWhoopsRequired($controller, $method)) {
                $event->setController(function () use ($response) {
                    return $response;
                });
                return;
            }
        }

        if (!($controller instanceof ControllerWithIdentification)) {
            return;
        }
        if ($this->identificationStatus->isIdentified()) {
            return;
        }
        if ($this->identificationStatus->isWifiFlowStarted()) {
            return;
        }
        if ($this->identificationStatus->isAlreadyTriedToAutoIdent()) {
            return;
        }

        $this->startWifiFlow($session);
        $this->identificationStatus->registerAutoIdentAttempt();

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
        $carriers = $this->carrierRepository->findEnabledCarriers();
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
                $this->carrierSelector->selectCarrier($carrierId);
            }
        } else {
            $ispDetectionData = $session->get('isp_detection_data');
            $carrierId        = $ispDetectionData['carrier_id'];
        }

        return $carrierId;
    }

    private function startWifiFlow(SessionInterface $session): Response
    {
        $this->dataStorage->storeValue('is_wifi_flow', true);

        return new RedirectResponse($this->routeProvider->getLinkToWifiFlowPage());

    }

    /**
     * @param Request $request
     * @param int     $carrierId
     * @return null|Response
     */
    private function doIdentify(Request $request, int $carrierId): ?Response
    {

        $response = null;
        try {
            $token    = $this->generator->generateToken();
            $result   = $this->identifier->identify(
                (int)$carrierId, $request,
                $token,
                $this->deviceDataProvider->get()
            );
            $response = $result->getOverridedResponse();

        } catch (FailedIdentificationException $exception) {

            $this->logger->error('Autoident failed');
            $response = $this->startWifiFlow($request->getSession());
        }

        return $response;
    }

    private function isRedirectToWhoopsRequired(object $controller, string $method): bool
    {
        $controllerReflection = new \ReflectionObject($controller);
        $methodReflection     = $controllerReflection->getMethod($method);

        $annotation = $this->annotationReader->getMethodAnnotation($methodReflection, NoRedirectToWhoops::class);

        return (bool)!$annotation;

    }
}