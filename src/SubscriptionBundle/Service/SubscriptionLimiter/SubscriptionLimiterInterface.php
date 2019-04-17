<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SubscriptionLimiterInterface
{
    public function isLimitReached(?CarrierLimiterData $carrierLimiterData, ?AffiliateLimiterData $affiliateLimiterData): bool;

    public function need2BeLimited(User $user): bool;

    public function startLimitingProcess(?CarrierLimiterData $carrierLimiterData, ?AffiliateLimiterData $affiliateLimiterData);

    public function finishLimitingProcess(?CarrierLimiterData $carrierLimiterData, ?AffiliateLimiterData $affiliateLimiterData);

    public function cancelLimitingProcess(?CarrierLimiterData $carrierLimiterData, ?AffiliateLimiterData $affiliateLimiterData);

    public function setLimiterData(SessionInterface $session, ?CarrierLimiterData $carrierLimiterData, ?AffiliateLimiterData $affiliateLimiterData);
}