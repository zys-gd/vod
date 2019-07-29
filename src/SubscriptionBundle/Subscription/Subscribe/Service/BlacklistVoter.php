<?php

namespace SubscriptionBundle\Subscription\Subscribe\Service;

use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Blacklist\BlacklistChecker;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlacklistVoter
 */
class BlacklistVoter
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var BlacklistChecker
     */
    private $blacklistChecker;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BlacklistVoter constructor
     *
     * @param RouterInterface  $router
     * @param BlacklistChecker $blacklistChecker
     * @param LoggerInterface  $logger
     */
    public function __construct(
        RouterInterface $router,
        BlacklistChecker $blacklistChecker,
        LoggerInterface $logger
    ) {
        $this->router             = $router;
        $this->blacklistChecker   = $blacklistChecker;
        $this->logger             = $logger;
    }
    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function isUserBlacklisted(SessionInterface $session): bool
    {
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($session);

        $this->logger->debug('Check user for blacklist', ['identification_token' => $identificationToken]);

        return $this->blacklistChecker->isUserBlacklisted($identificationToken);
    }

    /**
     * @param string $msisdn
     *
     * @return bool
     */
    public function isPhoneNumberBlacklisted(string $msisdn): bool
    {
        $this->logger->debug('Check phone number for blacklist', ['phone_number' => $msisdn]);

        return $this->blacklistChecker->isPhoneNumberBlacklisted($msisdn);
    }

    /**
     * @return RedirectResponse
     */
    public function createNotAllowedResponse()
    {
        $response = new RedirectResponse($this->getRedirectUrl());

        return $response;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->router->generate('blacklisted_user');
    }
}