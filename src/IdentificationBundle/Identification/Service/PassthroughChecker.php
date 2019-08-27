<?php


namespace IdentificationBundle\Identification\Service;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;

class PassthroughChecker
{
    /**
     * @var IdentificationHandlerProvider
     */
    private $handlerProvider;

    /**
     * PassthroughChecker constructor.
     *
     * @param IdentificationHandlerProvider $handlerProvider
     */
    public function __construct(IdentificationHandlerProvider $handlerProvider)
    {
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function isCarrierPassthrough(CarrierInterface $carrier): bool
    {
        $handler = $this->handlerProvider->get($carrier);
        return $handler instanceof HasPassthroughFlow;
    }
}