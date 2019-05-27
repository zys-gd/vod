<?php

namespace IdentificationBundle\WifiIdentification\Handler;

/**
 * Interface HasConsentFlow
 */
interface HasConsentPageFlow
{
    /**
     * @param string $mobileNumber
     *
     * @return bool
     */
    public function hasActiveSubscription(string $mobileNumber): bool;
}