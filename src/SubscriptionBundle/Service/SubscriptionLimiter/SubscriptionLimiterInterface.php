<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SubscriptionLimiterInterface
{
    public function isLimitReached(LimiterData $limiterData): bool;

    public function need2BeLimited(User $user): bool;

    public function startLimitingProcess(LimiterData $limiterData);

    public function finishLimitingProcess(LimiterData $limiterData);

    public function cancelLimitingProcess(LimiterData $limiterData);

    public function setLimiterData(SessionInterface $session, LimiterData $limiterData);
}