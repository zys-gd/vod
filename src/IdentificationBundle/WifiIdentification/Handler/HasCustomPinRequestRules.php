<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 15:11
 */

namespace IdentificationBundle\WifiIdentification\Handler;


use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;

interface HasCustomPinRequestRules
{

    public function getAdditionalPinRequestParams(): array;

    public function afterSuccessfulPinRequest(PinRequestResult $result): void;

    public function getPinRequestErrorMessage(PinRequestProcessException $exception): ?string;
}