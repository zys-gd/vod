<?php

namespace IdentificationBundle\WifiIdentification\Handler;

use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;

/**
 * Interface HasCustomPinVerifyRules
 */
interface HasCustomPinVerifyRules
{
    /**
     * @param PinRequestResult $pinRequestResult
     *
     * @return array
     */
    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult): array;

    /**
     * @param PinVerifyResult $parameters
     */
    public function afterSuccessfulPinVerify(PinVerifyResult $parameters): void;

    /**
     * @param \Exception $exception
     */
    public function afterFailedPinVerify(\Exception $exception): void;

    /**
     * @param PinVerifyResult $pinVerifyResult
     * @param string $phoneNumber
     *
     * @return string
     */
    public function getMsisdnFromResult(PinVerifyResult $pinVerifyResult, string $phoneNumber): string;
}