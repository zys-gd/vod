<?php

namespace IdentificationBundle\Identification\Handler\ConsentPageFlow;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HasConsentPageFlow
 */
interface HasCommonConsentPageFlow
{
    /**
     * @param Request          $request
     * @param CarrierInterface $carrier
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request, CarrierInterface $carrier): array;
}