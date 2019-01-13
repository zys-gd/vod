<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:52
 */

namespace IdentificationBundle\Callback\Handler;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class DefaultHandler implements IdentCallbackHandlerInterface, HasCommonFlow
{
    public function canHandle(int $carrierId): bool
    {
        return true;
    }

    public function afterSuccess(User $billableUser, ProcessResult $processResponse): void
    {
        // TODO: Implement afterSuccess() method.
    }

}