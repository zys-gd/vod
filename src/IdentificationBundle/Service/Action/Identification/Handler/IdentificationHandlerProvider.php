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
        $this->handlers[] = $handler;
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