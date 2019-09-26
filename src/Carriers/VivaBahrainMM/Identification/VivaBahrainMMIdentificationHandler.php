<?php

namespace Carriers\VivaBahrainMM\Identification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VivaBahrainMMIdentificationHandler
 */
class VivaBahrainMMIdentificationHandler implements IdentificationHandlerInterface, HasPassthroughFlow
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::VIVA_BAHRAIN_MM;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isCommonFlowShouldBeUsed(Request $request): bool
    {
        return $request->attributes->get('_route') != 'landing';
    }
}