<?php

namespace IdentificationBundle\Identification\Handler;

/**
 * Class AlreadySubscribedHandlerProvider
 */
class AlreadySubscribedHandlerProvider
{
    /**
     * @var AlreadySubscribedHandler[]
     */
    private $handlers = [];

    /**
     * @param AlreadySubscribedHandler $alreadySubscribedHandler
     */
    public function addHandler(AlreadySubscribedHandler $alreadySubscribedHandler): void
    {
        $this->handlers[] = $alreadySubscribedHandler;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return AlreadySubscribedHandler|null
     */
    public function get(int $billingCarrierId): ?AlreadySubscribedHandler
    {
        foreach ($this->handlers as $alreadySubscribedHandler) {
            if ($alreadySubscribedHandler->canHandle($billingCarrierId)) {
                return $alreadySubscribedHandler;
            }
        }

        return null;
    }
}