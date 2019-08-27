<?php

namespace SubscriptionBundle\Subscription\SubscribeBack\Handler;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;

class SubscribeBackHandlerProvider
{
    /**
     * @var SubscribeBackHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var SubscribeBackHandlerInterface
     */
    private $defaultHandler;

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

        return $this->defaultHandler;
    }
}