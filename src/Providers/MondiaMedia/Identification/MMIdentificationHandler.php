<?php


namespace Providers\MondiaMedia\Identification;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use Symfony\Component\HttpFoundation\Request;

class MMIdentificationHandler implements
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
        return in_array($carrier->getBillingCarrierId(), ID::MM_CARRIERS);
    }

    public function isCommonFlowShouldBeUsed(Request $request): bool
    {
        return $request->attributes->get('_route') != 'landing';
    }
}