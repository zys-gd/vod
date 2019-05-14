<?php

namespace IdentificationBundle\Carriers\VodafoneEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

/**
 * Class VodafoneEGIdentificationCallbackHandler
 */
class VodafoneEGIdentificationCallbackHandler implements IdentCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * VodafoneEGIdentificationCallbackHandler constructor
     *
     * @param IdentificationDataStorage $identificationDataStorage
     */
    public function __construct(IdentificationDataStorage $identificationDataStorage)
    {
        $this->identificationDataStorage = $identificationDataStorage;
    }

    /**
     * @param int $carrierId
     *
     * @return bool
     */
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }

    /**
     * @param User $user
     * @param ProcessResult $processResponse
     */
    public function afterSuccess(User $user, ProcessResult $processResponse): void
    {
        $subscriptionContractId = $processResponse->getProviderId();

        if ($subscriptionContractId) {
            $this->identificationDataStorage->storeSubscriptionContractId($subscriptionContractId);
        }
    }
}