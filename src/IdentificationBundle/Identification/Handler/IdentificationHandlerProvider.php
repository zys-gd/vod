<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:38
 */

namespace IdentificationBundle\Identification\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use Symfony\Component\HttpFoundation\Request;

class IdentificationHandlerProvider
{
    /**
     * @var DefaultHandler
     */
    private $defaultHandler;

    /**
     * @var IdentificationHandlerInterface[]
     */
    private $handlers = [];


    /**
     * IdentificationHandlerProvider constructor.
     *
     * @param DefaultHandler $defaultHandler
     */
    public function __construct(DefaultHandler $defaultHandler)
    {

        $this->defaultHandler = $defaultHandler;
    }

    public function addHandler(IdentificationHandlerInterface $handler): void
    {
        $this->ensureIsCorrect($handler);

        $this->handlers[] = $handler;
    }

    private function ensureIsCorrect(IdentificationHandlerInterface $handler)
    {
        $availableInterfaces = [
            HasCommonFlow::class,
            HasCustomFlow::class,
            HasHeaderEnrichment::class,
            HasCommonConsentPageFlow::class,
            HasPassthroughFlow::class
        ];

        $usedInterfaces = [];
        foreach ($availableInterfaces as $interface) {
            if ($handler instanceof $interface) {
                $usedInterfaces[] = $interface;
            }
        }

        if (count($usedInterfaces) <> 1) {
            $availableInterfaceString = json_encode($availableInterfaces);
            $handlerClass             = get_class($handler);
            throw new \InvalidArgumentException(
                sprintf('Handler `%s` should implement one of following interfaces `%s`', $handlerClass, $availableInterfaceString)
            );
        }
    }

    public function get(CarrierInterface $carrier): IdentificationHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($carrier)) {
                return $handler;
            }
        }

        return $this->defaultHandler;
    }

    public function getCommonHandler(): IdentificationHandlerInterface
    {
        return $this->defaultHandler;
    }
}