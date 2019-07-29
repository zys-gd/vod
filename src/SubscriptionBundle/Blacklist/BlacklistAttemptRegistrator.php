<?php

namespace SubscriptionBundle\Blacklist;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Psr\Log\LoggerInterface;

/**
 * Class BlacklistAttemptRegistrator
 */
class BlacklistAttemptRegistrator
{
    const TIME_LIMIT = 3600;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var ICacheService
     */
    private $cacheService;
    /**
     * @var BlacklistSaver
     */
    private $blacklistSaver;
    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BlacklistAttemptRegistrator constructor
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ICacheService $cacheService
     * @param BlacklistSaver $blacklistSaver
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        ICacheService $cacheService,
        BlacklistSaver $blacklistSaver,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        UserRepository $userRepository,
        LoggerInterface $logger
    ) {
        $this->carrierRepository         = $carrierRepository;
        $this->cacheService              = $cacheService;
        $this->blacklistSaver            = $blacklistSaver;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
        $this->userRepository            = $userRepository;
        $this->logger                    = $logger;
    }

    /**
     * @param string $identificationToken
     * @param int    $carrierId
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function registerSubscriptionAttempt(string $identificationToken, int $carrierId): bool
    {
        $this->logger->debug('Check user subscription attempts', ['identification_token' => $identificationToken]);

        if ($this->isSubscriptionAttemptRaised($identificationToken, $carrierId)) {
            return false;
        }

        $subscriptionTries = $this->getSubscriptionTries($identificationToken);

        $this->cacheService->saveCache($identificationToken, $subscriptionTries, self::TIME_LIMIT);

        if ($this->wifiIdentificationDataStorage->isWifiFlow()) {
            $user = $this->userRepository->findOneByIdentificationToken($identificationToken);
            $this->cacheService->saveCache($user->getIdentifier(), $subscriptionTries, self::TIME_LIMIT);
        }

        return true;
    }

    /**
     * @param string $identifier
     * @param int $carrierId
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isSubscriptionAttemptRaised(string $identifier, int $carrierId)
    {
        /** @var CarrierInterface $carrier */
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        if ($carrier->getSubscribeAttempts() === 0) { // unlimited attempts
            return false;
        }

        $subscriptionTries = $this->getSubscriptionTries($identifier);

        if (count($subscriptionTries) > $carrier->getSubscribeAttempts()) {
            $this->blacklistSaver->addToBlackList($identifier);
            $this->logger->debug('User raised subscription attempts and blacklisted');

            return true;
        }

        return false;
    }

    /**
     * @param string $identifier
     *
     * @return array
     */
    private function getSubscriptionTries(string $identifier): array
    {
        $subscriptionTries = $this->cacheService->hasCache($identifier)
            ? $this->cacheService->getValue($identifier)
            : [];

        $subscriptionTries[] = time();

        if (count($subscriptionTries) > 1) {
            while ($subscriptionTries[0] < (time() - self::TIME_LIMIT)) {
                array_shift($subscriptionTries);
            }
        }

        return $subscriptionTries;
    }
}