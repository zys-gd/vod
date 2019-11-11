<?php

namespace IdentificationBundle\Carriers\ZongPK;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class ZongPKIdentificationCallbackHandler implements
    IdentCallbackHandlerInterface,
    HasCommonFlow,
    HasPostPaidRestriction
{
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ID::ZONG_PAKISTAN;
    }

    public function afterSuccess(User $billableUser, ProcessResult $processResponse): void
    {
        // TODO: Implement onRenewSendSuccess() method.
    }
}