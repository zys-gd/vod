<?php

namespace SubscriptionBundle\Service\Action\SubscribeBack;

use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Service\Action\SubscribeBack\Handler\DefaultHandler;
use SubscriptionBundle\Service\Action\SubscribeBack\Handler\SubscribeBackHandlerInterface;

class SubscribeBackHandlerProvider
{
    /**
     * @var SubscribeBackHandlerInterface[]
     */
    private $handlers = [];

    public function __construct(DefaultHandler $handler)
    {
        $this->defaultHandler = $handler;
    }

    public function addHandler(SubscribeBackHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return SubscribeBackHandlerInterface
     */
    public function getHandler(CarrierInterface $carrier): SubscribeBackHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($carrier)) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException("Cannot get according handler");
    }
}