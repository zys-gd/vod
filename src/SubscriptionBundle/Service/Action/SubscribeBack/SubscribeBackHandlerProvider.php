<?php

namespace SubscriptionBundle\Service\Action\SubscribeBack;

use Symfony\Component\HttpFoundation\Request;

class SubscribeBackHandlerProvider
{
    /**
     * @var AbstractSubscribeBackHandler[]
     */
    private $handlers = [];

    public function __construct(... $handlers)
    {

        /** @var AbstractSubscribeBackHandler[] $handlers */
        foreach ($handlers as $handler) {

            if (!$handler instanceof AbstractSubscribeBackHandler) {
                throw new \InvalidArgumentException(sprintf('%s is not instance of %s', get_class($handler), AbstractSubscribeBackHandler::class));
            }

            $this->handlers[] = $handler;
        }
    }

    public function getHandler(Request $request): AbstractSubscribeBackHandler
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($request)) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException("Cannot get according handler");
    }
}