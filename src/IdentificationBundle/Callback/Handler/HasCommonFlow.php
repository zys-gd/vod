<?php

namespace IdentificationBundle\Callback\Handler;

use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;


/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:45
 */
interface HasCommonFlow
{
    public function afterSuccess(User $billableUser, ProcessResult $processResponse): void;
}