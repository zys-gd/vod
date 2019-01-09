<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 10:39
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Service\Action\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Service\Action\Identification\Handler\IdentificationHandlerProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
     * @var RouterInterface
     */
    private $router;


    /**
     * CommonFlowHandler constructor.
     * @param IdentProcess                  $identProcess
     * @param IdentificationHandlerProvider $handlerProvider
     * @param RequestParametersProvider     $parametersProvider
     */
    public function __construct(
        IdentProcess $identProcess,
        IdentificationHandlerProvider $handlerProvider,
        RequestParametersProvider $parametersProvider,
        RouterInterface $router
    )
    {
        $this->identProcess       = $identProcess;
        $this->handlerProvider    = $handlerProvider;
        $this->parametersProvider = $parametersProvider;
        $this->router             = $router;
    }

    public function process(Request $request, HasCommonFlow $handler, CarrierInterface $carrier): Response
    {
        $additionalParams = $handler->getAdditionalIdentificationParams($request);

        $parameters = $this->parametersProvider->prepareRequestParameters(
            $request,
            $request->getSession(),
            $additionalParams
        );

        $processResult = $this->identProcess->doIdent($parameters);

        if ($processResult->isPixel()) {
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


        return new Response();

    }
}