<?php

namespace IdentificationBundle\Carriers\VodafoneEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * VodafoneEGIdentificationCallbackHandler constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param IdentificationDataStorage $identificationDataStorage
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        IdentificationDataStorage $identificationDataStorage
    ) {
        $this->entityManager = $entityManager;
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
        $this->identificationDataStorage->storeValue('is_wifi_flow', false);

        $user->setProviderId($processResponse->getProviderId());
        $this->entityManager->flush();
    }
}