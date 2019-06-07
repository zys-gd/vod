<?php

namespace SubscriptionBundle\Service\Blacklist;

use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;

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
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * BlacklistAttemptRegistrator constructor
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ICacheService $cacheService
     * @param BlacklistSaver $blacklistSaver
     * @param IdentificationDataStorage $identificationDataStorage
     * @param UserRepository $userRepository
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        ICacheService $cacheService,
        BlacklistSaver $blacklistSaver,
        IdentificationDataStorage $identificationDataStorage,
        UserRepository $userRepository
    ) {
        $this->carrierRepository         = $carrierRepository;
        $this->cacheService              = $cacheService;
        $this->blacklistSaver            = $blacklistSaver;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->userRepository            = $userRepository;
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
        if ($this->isSubscriptionAttemptRaised($identificationToken, $carrierId)) {
            return false;
        }

        $subscriptionTries = $this->getSubscriptionTries($identificationToken);

        $this->cacheService->saveCache($identificationToken, $subscriptionTries, self::TIME_LIMIT);

        if ($this->identificationDataStorage->readValue('is_wifi_flow')) {
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