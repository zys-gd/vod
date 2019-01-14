<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 15:09
 */

namespace IdentificationBundle\WifiIdentification\Handler;


use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomPinVerifyRules
{

    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult): array;

    public function afterSuccessfulPinVerify(ProcessResult $parameters): void;

    public function afterFailedPinVerify(\Exception $exception): void;
}