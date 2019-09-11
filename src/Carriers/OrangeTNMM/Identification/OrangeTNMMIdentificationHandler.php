<?php

namespace Carriers\OrangeTNMM\Identification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use Symfony\Component\HttpFoundation\Request;


class OrangeTNMMIdentificationHandler implements
    IdentificationHandlerInterface,
    HasPassthroughFlow
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ORANGE_TUNISIA_MM;
    }

    public function isCommonFlowShouldBeUsed(Request $request): bool
    {
        return $request->attributes->get('_route') != 'landing';
    }
}