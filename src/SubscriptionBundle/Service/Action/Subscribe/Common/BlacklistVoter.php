<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Common;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Service\BlackListService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class BlacklistVoter
{
    const TIME_LIMIT = 3600;    //in seconds

    /**
     * @var ICacheService
     */
    private $cacheService;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $subNotAllowedRoute;
    /**
     * @var BlackListService
     */
    private $blackListService;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * BlacklistVoter constructor
     *
     * @param ICacheService     $cacheService
     * @param RouterInterface   $router
     * @param BlackListService  $blackListService
     * @param UserRepository    $userRepository
     * @param LoggerInterface   $logger
     * @param string            $subNotAllowedRoute
     * @param CarrierRepository $carrierRepository
     */
    public function __construct(
        ICacheService $cacheService,
        RouterInterface $router,
        BlackListService $blackListService,
        UserRepository $userRepository,
        LoggerInterface $logger,
        string $subNotAllowedRoute,
        CarrierRepository $carrierRepository
    )
    {
        $this->cacheService       = $cacheService;
        $this->router             = $router;
        $this->userRepository     = $userRepository;
        $this->logger             = $logger;
        $this->subNotAllowedRoute = $subNotAllowedRoute;
        $this->blackListService   = $blackListService;
        $this->carrierRepository  = $carrierRepository;
    }

    public function isInBlacklist(SessionInterface $session)
    {
        $data         = IdentificationFlowDataExtractor::extractIdentificationData($session);
        $sessionToken = $data['identification_token'] ?? null;

        return $this->blackListService->isBlacklisted($sessionToken);
    }

    /**
     * @param SessionInterface $session
     *
     * @return bool
     * @throws \Exception
     */
    public function deductSubscriptionAttempt(SessionInterface $session): bool
    {
        $data         = IdentificationFlowDataExtractor::extractIdentificationData($session);
        $sessionToken = $data['identification_token'] ?? null;
        $ispData      = IdentificationFlowDataExtractor::extractIspDetectionData($session);

        /** @var Carrier $carrier */
        $carrier = $this->carrierRepository->findOneByBillingId($ispData['carrier_id']);
        if ($carrier->getSubscribeAttempts() === 0) { // unlimited attempts
            return true;
        }

        $subscriptionTries = $this->cacheService->hasCache($sessionToken)
            ? $this->cacheService->getValue($sessionToken)
            : [];

        $subscriptionTries[] = time();
        $subscriptionTries   = $this->removeOldTimestamps($subscriptionTries);
        if (count($subscriptionTries) > $carrier->getSubscribeAttempts()) {
            $this->blackListService->addToBlackList($sessionToken);
            return false;
        }

        $this->cacheService->saveCache($sessionToken, $subscriptionTries, self::TIME_LIMIT);

        return true;
    }

    /**
     * @return RedirectResponse
     */
    public function createNotAllowedResponse()
    {
        $response = new RedirectResponse($this->router->generate($this->subNotAllowedRoute));
        return $response;
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