<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 16:55
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class MobilinkPKIdentificationCallbackHandler implements
    IdentCallbackHandlerInterface,
    HasCommonFlow,
    HasPostPaidRestriction
{
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ID::MOBILINK_PAKISTAN;
    }

    public function afterSuccess(User $billableUser, ProcessResult $processResponse): void
    {
        // TODO: Implement onRenewSendSuccess() method.
    }
}