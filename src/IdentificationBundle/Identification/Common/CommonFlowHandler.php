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
use IdentificationBundle\Identification\Common\Pixel\PixelIdentHandler;
use IdentificationBundle\Identification\Common\Redirect\RedirectIdentHandler;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @var PixelIdentHandler
     */
    private $pixelIdentHandler;
    /**
     * @var RedirectIdentHandler
     */
    private $redirectIdentHandler;
    /**
     * @var RouteProvider
     */
    private $routeProvider;


    /**
     * CommonFlowHandler constructor.
     * @param IdentProcess                  $identProcess
     * @param IdentificationHandlerProvider $handlerProvider
     * @param RequestParametersProvider     $parametersProvider
     * @param RouteProvider                 $routeProvider
     * @param PixelIdentHandler             $pixelIdentHandler
     * @param RedirectIdentHandler          $redirectIdentHandler
     */
    public function __construct(
        IdentProcess $identProcess,
        IdentificationHandlerProvider $handlerProvider,
        RequestParametersProvider $parametersProvider,
        RouteProvider $routeProvider,
        PixelIdentHandler $pixelIdentHandler,
        RedirectIdentHandler $redirectIdentHandler

    )
    {
        $this->identProcess         = $identProcess;
        $this->handlerProvider      = $handlerProvider;
        $this->parametersProvider   = $parametersProvider;
        $this->pixelIdentHandler    = $pixelIdentHandler;
        $this->redirectIdentHandler = $redirectIdentHandler;
        $this->routeProvider        = $routeProvider;
    }

    public function process(
        Request $request,
        HasCommonFlow $handler,
        string $token,
        CarrierInterface $carrier
    ): Response
    {
        $additionalParams = $handler->getAdditionalIdentificationParams($request);
        $redirectUrl      = $request->get('location', $this->routeProvider->getLinkToHomepage());
        $parameters       = $this->parametersProvider->prepareRequestParameters(
            $token,
            $carrier->getBillingCarrierId(),
            $request->getClientIp(),
            $redirectUrl,
            $request->headers->all(),
            $additionalParams
        );

        $processResult = $this->identProcess->doIdent($parameters);

        if ($processResult->isPixel()) {
            return $this->pixelIdentHandler->doHandle($request, $processResult, $carrier);
        } elseif ($processResult->isRedirectRequired()) {
            return $this->redirectIdentHandler->doHandle($processResult);
        }


        return new Response();

    }

}