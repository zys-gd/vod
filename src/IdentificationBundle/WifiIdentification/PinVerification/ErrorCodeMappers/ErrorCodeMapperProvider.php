<?php


namespace IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers;


class ErrorCodeMapperProvider
{
    /**
     * @var ErrorCodeMapperInterface[]
     */
    private $handlers = [];

    /**
     * @param ErrorCodeMapperInterface $handler
     */
    public function addHandler(ErrorCodeMapperInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return ErrorCodeMapperInterface|null
     */
    public function get(int $billingCarrierId): ?ErrorCodeMapperInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($billingCarrierId)) {
                return $handler;
            }
        }

        return null;
    }
}