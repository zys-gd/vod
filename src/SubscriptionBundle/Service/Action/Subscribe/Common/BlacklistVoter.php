<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Common;

use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Service\BlackListService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class BlacklistVoter
{
    const TIME_LIMIT = 3600;    //in seconds
    const ATTEMPTS_LIMIT = 5;
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
     * @var string
     */
    private $blacklistedUserRoute;
    /**
     * @var BlackListService
     */
    private $blackListService;

    /**
     * BlacklistVoter constructor
     *
     * @param ICacheService $cacheService
     * @param RouterInterface $router
     * @param BlackListService $blackListService
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @param string $subNotAllowedRoute
     * @param string $blacklistedUserRoute
     */
    public function __construct(
        ICacheService $cacheService,
        RouterInterface $router,
        BlackListService $blackListService,
        UserRepository $userRepository,
        LoggerInterface $logger,
        string $subNotAllowedRoute,
        string $blacklistedUserRoute
    ) {
        $this->cacheService       = $cacheService;
        $this->router             = $router;
        $this->userRepository     = $userRepository;
        $this->logger             = $logger;
        $this->subNotAllowedRoute = $subNotAllowedRoute;
        $this->blackListService   = $blackListService;
        $this->blacklistedUserRoute = $blacklistedUserRoute;
    }

    /**
     * @param Request $request
     *
     * @return bool|RedirectResponse
     */
    public function checkIfSubscriptionRestricted(Request $request)
    {
        $session      = $request->getSession();
        $blockingTime = $session->get('subscription_not_allowed', false);

        if ($blockingTime && time() - strtotime($blockingTime) < self::TIME_LIMIT) {
            $session->set('subscription_not_allowed', date('Y-m-d H:i:s'));
            $this->logger->debug('Blocking time have not finished yet', [$blockingTime]);

            return $this->createNotAllowedResponse();
        }

        $data         = IdentificationFlowDataExtractor::extractIdentificationData($request->getSession());
        $sessionToken = $data['identification_token'] ?? null;

        if (empty($sessionToken)) {
            $this->logger->debug('No identification token was found. Subscription is not allowed');

            return $this->createNotAllowedResponse();
        } elseif ($this->blackListService->isBlacklisted($sessionToken)) {
            $this->logger->debug('User in black list. Subscription is not allowed');

            return new RedirectResponse($this->router->generate($this->blacklistedUserRoute));
        }

        if (!$this->cacheService->hasCache($sessionToken)) {
            $savedValue = [
                'added_at' => date('Y-m-d H:i:s'),
            ];
        } else {
            $savedValue = $this->cacheService->getValue($sessionToken);

            if (empty($savedValue['added_at']) || !strtotime($savedValue['added_at'])) {
                return $this->createNotAllowedResponse();
            }

            $dateDiff = time() - strtotime($savedValue['added_at']);
            if ($dateDiff >= self::TIME_LIMIT) {
                if (isset($savedValue['updated_at'])
                    && \is_array($savedValue['updated_at'])
                    && strtotime($savedValue['updated_at'][0])
                ) {
                    $savedValue['added_at'] = array_shift($savedValue['updated_at']);
                    while ($savedValue['updated_at']) {
                        if (time() - strtotime($savedValue['updated_at'][0]) >= self::TIME_LIMIT) {
                            array_shift($savedValue['updated_at']);
                            continue;
                        }
                        break;
                    }
                }
            } elseif (isset($savedValue['updated_at']) && \count($savedValue['updated_at']) >= self::ATTEMPTS_LIMIT - 2) {
                $this->blackListService->addToBlackList($sessionToken);

                return $this->createNotAllowedResponse();
            }

            $savedValue['updated_at'][] = date('Y-m-d H:i:s');
        }

        $this->cacheService->saveCache($sessionToken, $savedValue, self::TIME_LIMIT);

        return false;
    }

    /**
     * @return RedirectResponse
     */
    private function createNotAllowedResponse()
    {
        $response = new RedirectResponse($this->router->generate($this->subNotAllowedRoute));
        return $response;
    }
}