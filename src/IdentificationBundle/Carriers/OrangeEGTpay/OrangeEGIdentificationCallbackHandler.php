<?php

namespace IdentificationBundle\Carriers\OrangeEGTpay;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

/**
 * Class OrangeEGIdentificationCallbackHandler
 */
class OrangeEGIdentificationCallbackHandler implements IdentCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @param int $carrierId
     *
     * @return bool
     */
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ID::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param User $user
     * @param ProcessResult $processResponse
     */
    public function afterSuccess(User $user, ProcessResult $processResponse): void
    {

    }
}