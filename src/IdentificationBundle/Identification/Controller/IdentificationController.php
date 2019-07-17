<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:54
 */

namespace IdentificationBundle\Identification\Controller;


use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Identifier;
use IdentificationBundle\Identification\IdentifierByUrl;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IdentificationController extends AbstractController
{
    /**
     * @var Identifier
     */
    private $identifier;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var IdentifierByUrl
     */
    private $identifierByUrl;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * IdentificationController constructor.
     *
     * @param Identifier                $identifier
     * @param TokenGenerator            $generator
     * @param IdentifierByUrl           $identifierByUrl
     * @param RouteProvider             $provider
     * @param IdentificationDataStorage $identificationDataStorage
     * @param LoggerInterface           $logger
     */
    public function __construct(
        Identifier $identifier,
        TokenGenerator $generator,
        IdentifierByUrl $identifierByUrl,
        RouteProvider $provider,
        IdentificationDataStorage $identificationDataStorage,
        LoggerInterface $logger
    )
    {
        $this->identifier                = $identifier;
        $this->tokenGenerator            = $generator;
        $this->identifierByUrl           = $identifierByUrl;
        $this->routeProvider             = $provider;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->logger = $logger;
    }

    /**
     * @Route("/identify",name="identify_and_subscribe")
     * @param Request    $request
     * @param DeviceData $deviceData
     * @return Response
     */
    public function identifyAndSubscribeAction(Request $request, DeviceData $deviceData): Response
    {
        if ($urlId = $request->get('urlId', '')) {
            // V1->V2 backward compatibility redirect
            return $this->redirectToRoute('identify_by_url', ['urlId' => $urlId]);
        }

        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($request->getSession());
        if ($identificationToken) {
            throw new BadRequestHttpException('You are already identified');
        }

        if (!$billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession())) {
            throw new BadRequestHttpException('Isp data missing');
        }

        $token = $this->tokenGenerator->generateToken();

        $this->logger->debug('Start ident from action', []);
        $result = $this->identifier->identify(
            $billingCarrierId,
            $request,
            $token,
            $deviceData
        );
        $this->logger->debug('Finish ident from action', ['result' => $result]);
        $this->identificationDataStorage->setSubscribeAfterIdent();

        if ($customResponse = $result->getOverridedResponse()) {
            return $customResponse;
        } else {
            return $this->redirectToRoute($this->routeProvider->getLinkToLanding());
        }
    }

    /**
     * @Route("/identify-by-url",name="identify_by_url")
     * @param Request $request
     * @return Response
     */
    public function identifyByUrlAction(Request $request)
    {
        if (!$urlId = $request->get('urlId', '')) {
            throw new BadRequestHttpException('`urlId` is missing');
        }

        try {
            $this->identifierByUrl->doIdentify($urlId);
        } catch (FailedIdentificationException $exception) {

        }

        return $this->redirect($this->routeProvider->getLinkToHomepage());

    }
}