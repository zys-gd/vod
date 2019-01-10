<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:27
 */

namespace IdentificationBundle\Service\Callback\Handler;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomFlow
{
    public function process(ProcessResult $result, CarrierInterface $carrier): void;
}