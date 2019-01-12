<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 12:10
 */

namespace IdentificationBundle\Identification\Handler\CommonFlow;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomPixelIdent
{
    public function onConfirm(ProcessResult $processResult): void;
}