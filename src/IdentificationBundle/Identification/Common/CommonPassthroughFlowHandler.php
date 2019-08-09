<?php

namespace IdentificationBundle\Identification\Common;

use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Common\Async\AsyncIdentStarter;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;


class CommonPassthroughFlowHandler
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;

    /**
     * @var TokenGenerator
     */
    private $generator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestParametersProvider
     */
    private $requestParametersProvider;

    /**
     * @var IdentProcess
     */
    private $identProcess;

    /**
     * @var AsyncIdentStarter
     */
    private $asyncIdentStarter;
    /**
     * @var PassthroughProcess
     */
    private $passthroughProcess;

    /**
     * ConsentPageFlowHandler constructor
     *
     * @param RouterInterface           $router
     * @param IdentificationDataStorage $dataStorage
     * @param TokenGenerator            $generator
     * @param RequestParametersProvider $requestParametersProvider
     * @param IdentProcess              $identProcess
     * @param AsyncIdentStarter         $asyncIdentStarter
     * @param PassthroughProcess        $passthroughProcess
     */
    public function __construct(
        RouterInterface $router,
        IdentificationDataStorage $dataStorage,
        TokenGenerator $generator,
        RequestParametersProvider $requestParametersProvider,
        IdentProcess $identProcess,
        AsyncIdentStarter $asyncIdentStarter,
        PassthroughProcess $passthroughProcess
    )
    {
        $this->router                    = $router;
        $this->dataStorage               = $dataStorage;
        $this->generator                 = $generator;
        $this->requestParametersProvider = $requestParametersProvider;
        $this->identProcess              = $identProcess;
        $this->asyncIdentStarter         = $asyncIdentStarter;
        $this->passthroughProcess        = $passthroughProcess;
    }

    /**
     * @param Request            $request
     * @param HasPassthroughFlow $handler
     * @param CarrierInterface   $carrier
     * @param string             $token
     *
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function process(Request $request,
        HasPassthroughFlow $handler,
        CarrierInterface $carrier,
        string $token): Response
    {

        $additionalParams = $handler->getAdditionalIdentificationParams($request);
        $successUrl       = $this->router->generate('subscription.passthrough_page_subscribe', [], RouterInterface::ABSOLUTE_URL);
        $waitPageUrl      = $this
            ->router
            ->generate('wait_for_callback', ['successUrl' => $successUrl], RouterInterface::ABSOLUTE_URL);

        $parameters = $this->requestParametersProvider->prepareRequestParameters(
            $token,
            $carrier->getBillingCarrierId(),
            $request->getClientIp(),
            $waitPageUrl,
            $request->headers->all(),
            $additionalParams
        );

        $passthrowLink = $this->passthroughProcess->runPassthrough($parameters);

        $this->dataStorage->storeValue('passthroughFlow[token]', $this->generator->generateToken());

        return new RedirectResponse($passthrowLink);
    }
}