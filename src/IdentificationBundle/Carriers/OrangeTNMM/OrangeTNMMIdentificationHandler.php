<?php

namespace IdentificationBundle\Carriers\OrangeTNMM;

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

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
    }
}