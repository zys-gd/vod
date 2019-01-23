<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.01.19
 * Time: 13:58
 */

namespace IdentificationBundle\Identification\Common;


use IdentificationBundle\Identification\Handler\HasConsentPageFlow;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;

class ConsentPageFlowHandler
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
     * @var RouteProvider
     */
    private $router;


    /**
     * ConsentPageFlowHandler constructor.
     * @param RouteProvider             $router
     * @param IdentificationDataStorage $dataStorage
     * @param TokenGenerator            $generator
     */
    public function __construct(
        RouteProvider $router,
        IdentificationDataStorage $dataStorage,
        TokenGenerator $generator
    )
    {
        $this->dataStorage = $dataStorage;
        $this->generator   = $generator;
        $this->router      = $router;
    }

    public function process(Request $request, HasConsentPageFlow $handler): void
    {
        $handler->onProcess($request);

        $this->dataStorage->storeValue('consentFlow[token]', $this->generator->generateToken());
    }
}