<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.05.19
 * Time: 14:32
 */

namespace SubscriptionBundle\Service\Blacklist;


use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * BlacklistAttemptRegistrator constructor.
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ICacheService              $cacheService
     * @param BlacklistSaver             $blacklistSaver
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        ICacheService $cacheService,
        BlacklistSaver $blacklistSaver
    )
    {
        $this->carrierRepository = $carrierRepository;
        $this->cacheService      = $cacheService;
        $this->blacklistSaver    = $blacklistSaver;
    }


    /**
     * @param string $identificationToken
     * @param int    $carrierId
     * @return bool
     * @throws \Exception
     */
    public function registerSubscriptionAttempt(string $identificationToken, int $carrierId): bool
    {

        /** @var CarrierInterface $carrier */
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        if ($carrier->getSubscribeAttempts() === 0) { // unlimited attempts
            return true;
        }

        $subscriptionTries = $this->cacheService->hasCache($identificationToken)
            ? $this->cacheService->getValue($identificationToken)
            : [];

        $subscriptionTries[] = time();
        $subscriptionTries   = $this->removeOldTimestamps($subscriptionTries);
        if (count($subscriptionTries) > $carrier->getSubscribeAttempts()) {
            $this->blacklistSaver->addToBlackList($identificationToken);
            return false;
        }

        $this->cacheService->saveCache($identificationToken, $subscriptionTries, self::TIME_LIMIT);

        return true;
    }

    private function removeOldTimestamps($savedValue)
    {
        if (count($savedValue) > 1) {
            while ($savedValue[0] < (time() - self::TIME_LIMIT)) {
                array_shift($savedValue);
            }
        }
        return $savedValue;
    }
}