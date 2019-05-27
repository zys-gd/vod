<?php

namespace IdentificationBundle\WifiIdentification\Handler;

use SubscriptionBundle\Entity\Subscription;

/**
 * Interface HasConsentFlow
 */
interface HasConsentPageFlow
{
    /**
     * @param string $mobileNumber
     *
     * @return Subscription|null
     */
    public function getExistingSubscription(string $mobileNumber): ?Subscription;
}