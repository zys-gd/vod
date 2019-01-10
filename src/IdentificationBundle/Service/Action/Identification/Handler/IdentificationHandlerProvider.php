<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:38
 */

namespace IdentificationBundle\Service\Action\Identification\Handler;


use IdentificationBundle\Entity\CarrierInterface;

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
        $availableInterfaceString = json_encode([
            HasCommonFlow::class,
            HasCustomFlow::class
        ]);
        $handlerClass             = get_class($handler);

        if ((!$handler instanceof HasCommonFlow) && (!$handler instanceof HasCustomFlow)) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` should implement one of following two interfaces `%s`', $handlerClass, $availableInterfaceString));
        }

        if ($handler instanceof HasCommonFlow && $handler instanceof HasCustomFlow) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` cannot implement both flows. Please select one of `%s`', $handlerClass, $availableInterfaceString));
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
}