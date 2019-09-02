<?php

namespace IdentificationBundle\Carriers\HutchID;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;


class HutchIDIdentificationHandler implements
    IdentificationHandlerInterface,
    HasHeaderEnrichment
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::HUTCH_INDONESIA;
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

    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function getMsisdn(Request $request): ?string
    {
        return $request->headers->get('x-msp-msisdn');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function needHandle(Request $request): bool
    {
        return true;
    }
}