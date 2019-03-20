<?php

namespace SubscriptionBundle\Service\Callback\Impl;

use Symfony\Component\HttpFoundation\Request;

class CarrierCallbackHandlerProvider
{
    /**
     * @var CarrierCallbackHandlerInterface[]
     */
    private $handlers = [];
    /**
     * @var DefaultHandler
     */
    private $defaultHandler;

    /**
     * CarrierCallbackHandlerProvider constructor.
     */
    public function __construct(DefaultHandler $defaultHandler)
    {
        $this->defaultHandler = $defaultHandler;
    }


    /**
     * @param CarrierCallbackHandlerInterface $handler
     */
    public function addHandler(CarrierCallbackHandlerInterface $handler, string $type)
    {
        $this->ensureIsCorrect($handler);

        $this->handlers[$type][] = $handler;
    }

    /**
     * @param Request $request
     * @param string  $type
     * @return CarrierCallbackHandlerInterface
     */
    public function getHandler(string $carrierId, Request $request, string $type): CarrierCallbackHandlerInterface
    {
        $availableHandlers = $this->handlers[$type] ?? [];

        /** @var CarrierCallbackHandlerInterface $handler */
        foreach ($availableHandlers as $handler) {
            if ($handler->canHandle($request, (int)$carrierId)) {
                return $handler;
            }
        }
        return $this->defaultHandler;
    }

    private function ensureIsCorrect(CarrierCallbackHandlerInterface $handler)
    {
        $availableInterfaceString = json_encode([
            HasCustomFlow::class,
            HasCommonFlow::class
        ]);
        $handlerClass             = get_class($handler);

        if ((!$handler instanceof HasCommonFlow) && (!$handler instanceof HasCustomFlow)) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` should implement one of following two interfaces `%s`', $handlerClass, $availableInterfaceString));
        }

        if ($handler instanceof HasCommonFlow && $handler instanceof HasCustomFlow) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` cannot implement both flows. Please select one of `%s`', $handlerClass, $availableInterfaceString));

        }

    }

}