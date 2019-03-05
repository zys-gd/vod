<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.03.19
 * Time: 17:17
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Notification;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\LanguageInterface;
use IdentificationBundle\Repository\LanguageRepositoryInterface;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerInterface;

class TelenorPKNotificationHandler implements NotificationHandlerInterface
{
    /**
     * @var LanguageRepositoryInterface
     */
    private $languageRepository;


    /**
     * TelenorPKNotificationHandler constructor.
     * @param LanguageRepositoryInterface $languageRepository
     */
    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }

    public function isNotificationShouldBeSent(): bool
    {
        return true;
    }

    public function isProcessIdUsedInNotification(): bool
    {
        return false;
    }

    public function getSmsLanguage(): LanguageInterface
    {
        return $this->languageRepository->findByCode('ur');
    }

    public function getMessageNamespace(): ?string
    {
        return 'dot.telenorpk';
    }
}