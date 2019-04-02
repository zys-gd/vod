<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 16:55
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class MobilinkPKIdentificationCallbackHandler implements
    IdentCallbackHandlerInterface,
    HasCommonFlow
{
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::MOBILINK_PAKISTAN;
    }

    public function afterSuccess(User $billableUser, ProcessResult $processResponse): void
    {
        // TODO: Implement onRenewSendSuccess() method.
    }
}