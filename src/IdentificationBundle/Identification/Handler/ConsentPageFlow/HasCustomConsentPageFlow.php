<?php

namespace IdentificationBundle\Identification\Handler\ConsentPageFlow;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface HasCustomConsentFlowPage
 */
interface HasCustomConsentPageFlow
{
    /**
     * @param Request $request
     * @param CarrierInterface $carrier
     * @param string $token
     *
     * @return Response
     */
    public function process(Request $request, CarrierInterface $carrier, string $token): Response;
}