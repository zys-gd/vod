<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;


use IdentificationBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SubscriptionLimiterInterface
{
    public function isLimitReached(SessionInterface $session): bool;

    public function need2BeLimited(User $user): bool;

    public function startLimitingProcess(SessionInterface $session);

    public function finishLimitingProcess(SessionInterface $session);

    public function cancelLimitingProcess(SessionInterface $session);
}