<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 10:39
 */

namespace IdentificationBundle\Identification\Common;


use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Common\Pixel\PixelIdentStarter;
use IdentificationBundle\Identification\Common\Async\AsyncIdentStarter;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CommonFlowHandler
{
    /**
     * @var IdentProcess
     */
    private $identProcess;
    /**
     * @var IdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var RequestParametersProvider
     */
    private $parametersProvider;

    /**
     * @var PixelIdentStarter
     */
    private $pixelIdentStarter;
    /**
     * @var AsyncIdentStarter
     */
    private $asyncIdentStarter;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * CommonFlowHandler constructor.
     * @param IdentProcess                  $identProcess
     * @param IdentificationHandlerProvider $handlerProvider
     * @param RequestParametersProvider     $parametersProvider
     * @param RouteProvider                 $routeProvider
     * @param PixelIdentStarter             $pixelIdentStarter
     * @param AsyncIdentStarter             $asyncIdentStarter
     * @param RouterInterface               $router
     */
    public function __construct(
        IdentProcess $identProcess,
        IdentificationHandlerProvider $handlerProvider,
        RequestParametersProvider $parametersProvider,
        RouteProvider $routeProvider,
        PixelIdentStarter $pixelIdentStarter,
        AsyncIdentStarter $asyncIdentStarter,
        RouterInterface $router

    )
    {
        $this->identProcess       = $identProcess;
        $this->handlerProvider    = $handlerProvider;
        $this->parametersProvider = $parametersProvider;
        $this->pixelIdentStarter  = $pixelIdentStarter;
        $this->asyncIdentStarter  = $asyncIdentStarter;
        $this->routeProvider      = $routeProvider;
        $this->router             = $router;
    }

    public function process(
        Request $request,
        HasCommonFlow $handler,
        string $token,
        CarrierInterface $carrier
    ): Response
    {
        $additionalParams = $handler->getAdditionalIdentificationParams($request);
        $successUrl       = $request->get('location', $this->routeProvider->getLinkToHomepage());
        $waitPageUrl      = $this->router->generate('wait_for_callback', ['successUrl' => $successUrl], RouterInterface::ABSOLUTE_URL);
        $parameters       = $this->parametersProvider->prepareRequestParameters(
            $token,
            $carrier->getBillingCarrierId(),
            $request->getClientIp(),
            $waitPageUrl,
            $request->headers->all(),
            $additionalParams
        );

        $processResult = $this->identProcess->doIdent($parameters);

        if ($processResult->isPixel()) {
            return $this->pixelIdentStarter->start($request, $processResult, $carrier);
        } elseif ($processResult->isRedirectRequired()) {
            return $this->asyncIdentStarter->start($processResult, $token);
        }


        return new RedirectResponse($this->routeProvider->getLinkToHomepage());

    }

}