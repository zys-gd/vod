<?php

namespace IdentificationBundle\Identification\Handler;

use IdentificationBundle\Entity\CarrierInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HasConsentPageFlow
 */
interface HasConsentPageFlow
{
    /**
     * @param Request $request
     * @param CarrierInterface $carrier
     * @param string $token
     *
     * @return RedirectResponse
     */
    public function onProcess(Request $request, CarrierInterface $carrier, string $token): RedirectResponse;
}