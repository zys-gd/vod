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
     * PixelIdentStarter constructor.
     * @param RouterInterface $router
     * @param RouteProvider   $routeProvider
     */
    public function __construct(RouterInterface $router, RouteProvider $routeProvider)
    {
        $this->router        = $router;
        $this->routeProvider = $routeProvider;
    }

    public function start(Request $request, ProcessResult $processResult, CarrierInterface $carrier): RedirectResponse
    {
        try {
            $successUrl = $this->router->generate($request->attributes->get('_route'), [], RouterInterface::ABSOLUTE_URL);
        } catch (RouteNotFoundException $exception) {
            $successUrl = $this->routeProvider->getLinkToHomepage();
        }

        $pixelPageLink = $this->router->generate('show_pixel_page', [
            'pixelUrl'   => $processResult->getUrl(),
            'carrier'    => $carrier->getBillingCarrierId(),
            'processId'  => $processResult->getId(),
            'successUrl' => $successUrl
        ]);

        return new RedirectResponse($pixelPageLink);
    }
}