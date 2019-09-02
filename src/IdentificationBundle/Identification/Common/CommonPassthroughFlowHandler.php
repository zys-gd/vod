<?php

namespace IdentificationBundle\Identification\Common;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use IdentificationBundle\Identification\Service\PassthroughRequestPreparer;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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
     * @var PassthroughProcess
     */
    private $passthroughProcess;
    /**
     * @var PassthroughRequestPreparer
     */
    private $passthroughRequestPreparer;

    /**
     * ConsentPageFlowHandler constructor
     *
     * @param IdentificationDataStorage  $dataStorage
     * @param TokenGenerator             $generator
     * @param PassthroughProcess         $passthroughProcess
     * @param PassthroughRequestPreparer $passthroughRequestPreparer
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        TokenGenerator $generator,
        PassthroughProcess $passthroughProcess,
        PassthroughRequestPreparer $passthroughRequestPreparer
    )
    {
        $this->dataStorage                = $dataStorage;
        $this->generator                  = $generator;
        $this->passthroughProcess         = $passthroughProcess;
        $this->passthroughRequestPreparer = $passthroughRequestPreparer;
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
    public function process(
        Request $request,
        HasPassthroughFlow $handler,
        CarrierInterface $carrier,
        string $token
    ): Response
    {
        $parameters = $this->passthroughRequestPreparer->getProcessRequestParameters($request);

        $passthrowLink = $this->passthroughProcess->runPassthrough($parameters);

        $this->dataStorage->setIdentificationToken($parameters->clientId);

        return new RedirectResponse($passthrowLink);
    }
}