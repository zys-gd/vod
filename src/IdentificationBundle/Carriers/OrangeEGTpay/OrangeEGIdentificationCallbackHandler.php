<?php

namespace IdentificationBundle\Carriers\OrangeEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * VodafoneEGIdentificationCallbackHandler constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $carrierId
     *
     * @return bool
     */
    public function canHandle(int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param User $user
     * @param ProcessResult $processResponse
     */
    public function afterSuccess(User $user, ProcessResult $processResponse): void
    {
        $user->setProviderId($processResponse->getProviderId());
        $this->entityManager->flush();
    }
}