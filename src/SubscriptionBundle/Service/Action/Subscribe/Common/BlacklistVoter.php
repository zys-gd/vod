<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Common;

use App\Domain\Repository\CarrierRepository;
use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Service\Blacklist\BlacklistChecker;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlacklistVoter
 */
class BlacklistVoter
{
    /**
     * @var ICacheService
     */
    private $cacheService;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var BlacklistChecker
     */
    private $blacklistChecker;
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
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * BlacklistVoter constructor
     *
     * @param ICacheService     $cacheService
     * @param RouterInterface   $router
     * @param BlacklistChecker  $blacklistChecker
     * @param UserRepository    $userRepository
     * @param LoggerInterface   $logger
     * @param string            $subNotAllowedRoute
     * @param CarrierRepository $carrierRepository
     */
    public function __construct(
        ICacheService $cacheService,
        RouterInterface $router,
        BlacklistChecker $blacklistChecker,
        UserRepository $userRepository,
        LoggerInterface $logger,
        string $subNotAllowedRoute,
        CarrierRepository $carrierRepository
    ) {
        $this->cacheService       = $cacheService;
        $this->router             = $router;
        $this->blacklistChecker   = $blacklistChecker;
        $this->userRepository     = $userRepository;
        $this->logger             = $logger;
        $this->subNotAllowedRoute = $subNotAllowedRoute;
        $this->carrierRepository  = $carrierRepository;
    }

    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function isUserBlacklisted(SessionInterface $session): bool
    {
        $data         = IdentificationFlowDataExtractor::extractIdentificationData($session);
        $sessionToken = $data['identification_token'] ?? null;

        return $this->blacklistChecker->isUserBlacklisted($sessionToken);
    }

    /**
     * @param string $msisdn
     *
     * @return bool
     */
    public function isPhoneNumberBlacklisted(string $msisdn): bool
    {
        return $this->blacklistChecker->isPhoneNumberBlacklisted($msisdn);
    }

    /**
     * @return RedirectResponse
     */
    public function createNotAllowedResponse()
    {
        $response = new RedirectResponse($this->router->generate($this->subNotAllowedRoute));

        return $response;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->router->generate($this->subNotAllowedRoute);
    }
}