<?php

namespace IdentificationBundle\Identification\Common;

use HasPassthrough;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Common\Async\AsyncIdentStarter;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasConsentPageFlow;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCustomConsentPageFlow;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ConsentPageFlowHandler
 */
class CommonConsentPageFlowHandler
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
     * @param HasConsentPageFlow $handler
     * @param CarrierInterface   $carrier
     * @param string             $token
     *
     * @return Response
     */
    public function process(Request $request,
        HasConsentPageFlow $handler,
        CarrierInterface $carrier,
        string $token): Response
    {
        if ($handler instanceof HasCommonConsentPageFlow) {
            $additionalParams = $handler->getAdditionalIdentificationParams($request);
            $successUrl       = $this->router->generate('subscription.consent_page_subscribe', [], RouterInterface::ABSOLUTE_URL);
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

            if ($handler instanceof HasPassthrough) {
                $processResult = $this->passthroughProcess->runPassthrough($parameters);
            }
            else {
                $processResult = $this->identProcess->doIdent($parameters);
            }

            $this->dataStorage->storeValue('consentFlow[token]', $this->generator->generateToken());

            return $this->asyncIdentStarter->start($processResult, $token);
        }

        if ($handler instanceof HasCustomConsentPageFlow) {
            return $handler->process($request, $carrier, $token);
        }

        throw new \RuntimeException('Handlers for identification should have according interfaces');
    }
}