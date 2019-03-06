<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:22
 */

namespace SubscriptionBundle\Service\Action\Renew\Handler;


interface HasCommonFlow
{
    public function afterProcess(\SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result): void;
}