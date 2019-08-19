<?php

namespace IdentificationBundle\Carriers\ZainKSA;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

/**
 * Class ZainSAIdentificationCallbackHandler
 *
 */
class ZainKSAIdentificationCallbackHandler implements IdentCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @param int $carrierId
     *
     * @return bool
     */
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::ZAIN_SAUDI_ARABIA;
    }

    /**
     * @param User          $user
     * @param ProcessResult $processResponse
     */
    public function afterSuccess(User $user, ProcessResult $processResponse): void
    {
        // TODO: Implement afterSuccess() method.
    }
}