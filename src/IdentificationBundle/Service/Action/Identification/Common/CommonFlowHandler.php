<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 10:39
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Service\Action\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Service\Action\Identification\Handler\IdentificationHandlerProvider;
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
     * CommonFlowHandler constructor.
     * @param IdentProcess                  $identProcess
     * @param IdentificationHandlerProvider $handlerProvider
     * @param RequestParametersProvider     $parametersProvider
     */
    public function __construct(
        IdentProcess $identProcess,
        IdentificationHandlerProvider $handlerProvider,
        RequestParametersProvider $parametersProvider
    )
    {
        $this->identProcess       = $identProcess;
        $this->handlerProvider    = $handlerProvider;
        $this->parametersProvider = $parametersProvider;
    }

    public function process(Request $request, HasCommonFlow $handler): Response
    {
        $additionalParams = $handler->getAdditionalIdentificationParams($request);

        $parameters = $this->parametersProvider->prepareRequestParameters(
            $request,
            $request->getSession(),
            $additionalParams
        );

        $processResult = $this->identProcess->doIdent($parameters);

        return new Response();

    }
}