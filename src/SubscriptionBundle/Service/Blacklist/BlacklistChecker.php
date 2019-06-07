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
    ) {
        $this->userRepository      = $userRepository;
        $this->blackListRepository = $blackListRepository;
    }

    /**
     * @param string $identificationToken
     *
     * @return bool
     */
    public function isUserBlacklisted(string $identificationToken): bool
    {
        $user = $this->userRepository->findOneByIdentificationToken($identificationToken);

        return $this->isBlacklisted($user->getIdentifier());
    }

    /**
     * @param string $phoneNumber
     *
     * @return bool
     */
    public function isPhoneNumberBlacklisted(string $phoneNumber): bool
    {
        return $this->isBlacklisted($phoneNumber);
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    private function isBlacklisted(string $identifier): bool
    {
        try {
            /** @var BlackList $blackList */
            $blackList = $this->blackListRepository->findOneBy(['alias' => $identifier]);
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