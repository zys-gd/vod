<?php

namespace SubscriptionBundle\Service\Blacklist;

use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\BlackList;
use SubscriptionBundle\Repository\BlackListRepository;

/**
 * Class BlacklistChecker
 */
class BlacklistChecker
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var BlackListRepository
     */
    private $blackListRepository;


    /**
     * BlacklistChecker constructor
     *
     * @param UserRepository      $userRepository
     * @param BlackListRepository $blackListRepository
     */
    public function __construct(
        UserRepository $userRepository,
        BlackListRepository $blackListRepository
    )
    {
        $this->userRepository      = $userRepository;
        $this->blackListRepository = $blackListRepository;
    }

    /**
     * @param string $sessionToken
     *
     * @return bool
     */
    public function isBlacklisted(string $sessionToken): bool
    {
        try {
            $user = $this->userRepository->findOneByIdentificationToken($sessionToken);
            /** @var BlackList $blackList */
            $blackList = $this->blackListRepository->findOneBy(['alias' => $user->getIdentifier()]);
            $today     = new \DateTime();
            if ($blackList->getDuration() > 0
                && $blackList->getBanStart() < $today
                && $today < $blackList->getBanEnd()
                || $blackList->getDuration() == 0
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }


}