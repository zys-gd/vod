<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:25
 */

namespace SubscriptionBundle\Subscription\Notification\Impl;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use CommonDataBundle\Repository\Interfaces\LanguageRepositoryInterface;

class DefaultHandler implements NotificationHandlerInterface
{
    /**
     * @var LanguageRepositoryInterface
     */
    private $languageRepository;

    /**
     * DefaultHandler constructor.
     * @param LanguageRepositoryInterface $languageRepository
     */
    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
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
        return null;
    }
}