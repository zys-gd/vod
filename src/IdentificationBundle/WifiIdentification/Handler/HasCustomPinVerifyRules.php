<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 15:09
 */

namespace IdentificationBundle\WifiIdentification\Handler;


use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\WifiIdentification\Exception\WifiIdentConfirmException;

interface HasCustomPinVerifyRules
{

    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult): array;

    public function afterSuccessfulPinVerify(PinVerifyResult $parameters): void;

    public function afterFailedPinVerify(\Exception $exception): void;

    /**
     * @param PinVerifyResult $pinVerifyResult
     * @param string          $phoneNumber
     * @throws WifiIdentConfirmException
     * @return string
     */
    public function getMsisdnFromResult(PinVerifyResult $pinVerifyResult, string $phoneNumber): string;
}