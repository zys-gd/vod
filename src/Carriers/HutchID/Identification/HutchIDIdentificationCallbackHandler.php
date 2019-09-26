<?php

namespace Carriers\HutchID\Identification;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;


class HutchIDIdentificationCallbackHandler implements IdentCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @param int $carrierId
     *
     * @return bool
     */
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ID::HUTCH3_INDONESIA_DOT;
    }

    /**
     * @param User $user
     * @param ProcessResult $processResponse
     */
    public function afterSuccess(User $user, ProcessResult $processResponse): void
    {

    }
}