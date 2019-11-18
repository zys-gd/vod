<?php


namespace Carriers\OrangeEGMM\Identification;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use Symfony\Component\HttpFoundation\Request;

class OrangeEGMMIdentificationHandler implements
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
        return $carrier->getBillingCarrierId() === ID::ORANGE_EG_MM;
    }

    public function isCommonFlowShouldBeUsed(Request $request): bool
    {
        return $request->attributes->get('_route') != 'landing';
    }


}