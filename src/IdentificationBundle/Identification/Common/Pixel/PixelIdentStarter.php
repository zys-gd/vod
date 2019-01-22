<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 13:14
 */

namespace IdentificationBundle\Identification\Common\Pixel;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Identification\Service\SignatureHandler;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class PixelIdentStarter
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var SignatureHandler
     */
    private $signatureHandler;


    /**
     * PixelIdentStarter constructor.
     * @param RouterInterface  $router
     * @param RouteProvider    $routeProvider
     * @param SignatureHandler $signatureHandler
     */
    public function __construct(
        RouterInterface $router,
        RouteProvider $routeProvider,
        SignatureHandler $signatureHandler
    )
    {
        $this->router           = $router;
        $this->routeProvider    = $routeProvider;
        $this->signatureHandler = $signatureHandler;
    }

    public function start(Request $request, ProcessResult $processResult, CarrierInterface $carrier): RedirectResponse
    {
        try {
            $successUrl = $this->router->generate($request->attributes->get('_route'), [], RouterInterface::ABSOLUTE_URL);
        } catch (RouteNotFoundException $exception) {
            $successUrl = $this->routeProvider->getLinkToHomepage();
        }

        $parameters = [
            'pixelUrl'   => $processResult->getUrl(),
            'carrier'    => $carrier->getBillingCarrierId(),
            'processId'  => $processResult->getId(),
            'successUrl' => $successUrl,
        ];

        $parameters['signature'] = $this->signatureHandler->generateSign($parameters);
        $pixelPageLink           = $this->router->generate('show_pixel_page', $parameters);

        return new RedirectResponse($pixelPageLink);
    }
}