<?php

namespace IdentificationBundle\WifiIdentification\Handler;

use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\Exception\WifiIdentConfirmException;

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
     * @param User $user
     */
    public function afterSuccessfulPinVerify(PinVerifyResult $parameters, User $user): void;

    /**
     * @param \Exception $exception
     */
    public function afterFailedPinVerify(\Exception $exception): void;

    /**
     * @param PinVerifyResult $pinVerifyResult
     * @param string $phoneNumber
     *
     * @return string
     *
     * @throws WifiIdentConfirmException
     */
    public function getMsisdnFromResult(PinVerifyResult $pinVerifyResult, string $phoneNumber): string;
}