<?php

namespace SubscriptionBundle\Carriers\BeelineKZ\Notification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use CommonDataBundle\Repository\Interfaces\LanguageRepositoryInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\Impl\NotificationHandlerInterface;

/**
 * Class BeelineKZNotificationHandler
 */
class BeelineKZNotificationHandler implements NotificationHandlerInterface
{
    /**
     * @var LanguageRepositoryInterface
     */
    private $languageRepository;

    /**
     * BeelineKZNotificationHandler constructor
     *
     * @param LanguageRepositoryInterface $languageRepository
     */
    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::BEELINE_KAZAKHSTAN_DOT;
    }

    /**
     * @return bool
     */
    public function isNotificationShouldBeSent(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isProcessIdUsedInNotification(): bool
    {
        return false;
    }

    /**
     * @return LanguageInterface
     */
    public function getSmsLanguage(): LanguageInterface
    {
        return $this->languageRepository->findByCode('kk');
    }
}