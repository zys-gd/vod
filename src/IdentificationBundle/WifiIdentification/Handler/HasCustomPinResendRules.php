<?php

namespace IdentificationBundle\WifiIdentification\Handler;

use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;

/**
 * Interface HasCustomPinResendRules
 */
interface HasCustomPinResendRules
{
    /**
     * @param PinRequestResult $pinRequestResult
     *
     * @return array
     */
    public function getAdditionalPinResendParameters(PinRequestResult $pinRequestResult): array;
}