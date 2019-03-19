<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.03.19
 * Time: 12:04
 */

namespace SubscriptionBundle\Carriers\JazzPK\Notification;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\LanguageInterface;
use IdentificationBundle\Repository\LanguageRepositoryInterface;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerInterface;

class JazzPKNotificationHandler implements NotificationHandlerInterface
{
    private $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::MOBILINK_PAKISTAN;
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
        return $this->languageRepository->findByCode('en');
    }

    public function getMessageNamespace(): ?string
    {
        return 'dot.jazzpk';
    }
}