<?php

namespace SubscriptionBundle\Service\Blacklist;

use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BlacklistChecker constructor
     *
     * @param UserRepository $userRepository
     * @param BlackListRepository $blackListRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        UserRepository $userRepository,
        BlackListRepository $blackListRepository,
        LoggerInterface $logger
    ) {
        $this->userRepository      = $userRepository;
        $this->blackListRepository = $blackListRepository;
        $this->logger              = $logger;
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
                $this->logger->debug('User blacklisted');
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }
}