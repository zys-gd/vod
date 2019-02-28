<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 13:14
 */

namespace IdentificationBundle\Identification\Common\Pixel;


use ExtrasBundle\SignatureCheck\ParametersProvider;
use ExtrasBundle\SignatureCheck\SignatureHandler;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @var \ExtrasBundle\SignatureCheck\SignatureHandler
     */
    private $signatureHandler;
    /**
     * @var ParametersProvider
     */
    private $config;


    /**
     * PixelIdentStarter constructor.
     * @param RouterInterface                               $router
     * @param RouteProvider                                 $routeProvider
     * @param \ExtrasBundle\SignatureCheck\SignatureHandler $signatureHandler
     * @param ParametersProvider                            $config
     */
    public function __construct(
        RouterInterface $router,
        RouteProvider $routeProvider,
        SignatureHandler $signatureHandler,
        ParametersProvider $config
    )
    {
        $this->router           = $router;
        $this->routeProvider    = $routeProvider;
        $this->signatureHandler = $signatureHandler;
        $this->config           = $config;
    }

    public function start(Request $request, ProcessResult $processResult, CarrierInterface $carrier): RedirectResponse
    {
        $successUrl = $this->routeProvider->getLinkToHomepage();

        $parameters = [
            'pixelUrl'   => $processResult->getUrl(),
            'carrier'    => $carrier->getBillingCarrierId(),
            'processId'  => $processResult->getId(),
            'successUrl' => $successUrl,
        ];

        $signatureParam              = $this->config->getSignatureParameter();
        $parameters[$signatureParam] = $this->signatureHandler->generateSign($parameters);
        $pixelPageLink               = $this->router->generate('show_pixel_page', $parameters);

        return new RedirectResponse($pixelPageLink);
    }
}