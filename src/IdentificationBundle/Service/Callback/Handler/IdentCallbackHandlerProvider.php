<?php

namespace IdentificationBundle\Service\Callback\Handler;

class IdentCallbackHandlerProvider
{


    /**
     * @var IdentCallbackHandlerInterface[]
     */
    private $handlers = [];
    /**
     * @var DefaultHandler
     */
    private $defaultHandler;

    /**
     * IdentCallbackHandlerProvider constructor.
     * @param DefaultHandler $handler
     */
    public function __construct(DefaultHandler $handler)
    {
        $this->defaultHandler = $handler;
    }

    /**
     * @param IdentCallbackHandlerInterface $handler
     */
    public function addHandler(IdentCallbackHandlerInterface $handler)
    {
        $this->ensureIsCorrect($handler);

        $this->handlers[] = $handler;
    }

    /**
     * @param int $carrierId
     * @return IdentCallbackHandlerInterface
     */
    public function getHandler(int $carrierId): IdentCallbackHandlerInterface
    {

        /** @var IdentCallbackHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($carrierId)) {
                return $handler;
            }
        }
        return $this->defaultHandler;
    }

    private function ensureIsCorrect(IdentCallbackHandlerInterface $handler)
    {
        $availableInterfaceString = json_encode([
            HasCustomFlow::class,
            HasCommonFlow::class
        ]);

        $handlerClass = get_class($handler);

        if ((!$handler instanceof HasCommonFlow) && (!$handler instanceof HasCustomFlow)) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` should implement one of following two interfaces `%s`', $handlerClass, $availableInterfaceString));
        }

        if ($handler instanceof HasCommonFlow && $handler instanceof HasCustomFlow) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` cannot implement both flows. Please select one of `%s`', $handlerClass, $availableInterfaceString));

        }

    }

}