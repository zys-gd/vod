<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;


use IdentificationBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SubscriptionLimiterInterface
{
    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function isLimitReached(SessionInterface $session): bool;

    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function canStartProcess(SessionInterface $session): bool;

    /**
     * @param User $user
     *
     * @return bool
     */
    public function need2BeLimited(User $user): bool;

    /**
     * @param SessionInterface $session
     *
     * @return mixed
     */
    public function startLimitingProcess(SessionInterface $session);

    /**
     * @param SessionInterface $session
     *
     * @return mixed
     */
    public function finishLimitingProcess(SessionInterface $session);

    /**
     * @param SessionInterface $session
     *
     * @return mixed
     */
    public function cancelLimitingProcess(SessionInterface $session);
}