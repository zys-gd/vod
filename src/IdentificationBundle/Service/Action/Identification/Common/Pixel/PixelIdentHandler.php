<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 13:14
 */

namespace IdentificationBundle\Service\Action\Identification\Common\Pixel;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class PixelIdentHandler
{
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * PixelIdentHandler constructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function doHandle(Request $request, ProcessResult $processResult, CarrierInterface $carrier): RedirectResponse
    {
        try {
            $backUrl    = $this->router->generate($request->attributes->get('_route'), [], RouterInterface::ABSOLUTE_URL);
            $successUrl = $this->router->generate('identification_pixelident_confirmpixelident', [
                'backUrl'   => $backUrl,
                'processId' => $processResult->getId()
            ]);
        } catch (RouteNotFoundException $exception) {
            $successUrl = $this->router->generate('index', [], RouterInterface::ABSOLUTE_URL);
        }

        $pixelPageLink = $this->router->generate('identification_pixelident_showpixel', [
            'pixelUrl'   => $processResult->getUrl(),
            'carrier'    => $carrier->getBillingCarrierId(),
            'processId'  => $processResult->getId(),
            'successUrl' => $successUrl
        ]);

        return new RedirectResponse($pixelPageLink);
    }
}