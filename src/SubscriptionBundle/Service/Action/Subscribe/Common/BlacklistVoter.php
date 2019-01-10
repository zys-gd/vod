<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.07.18
 * Time: 12:19
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Common;


use AppBundle\Service\Interfaces\ICacheService;
use IdentificationBundle\Service\IdentificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use UserBundle\Service\UsersService;

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
     * @var IdentificationService
     */
    private $identificationService;
    /**
     * @var string
     */
    private $tokenName;
    /**
     * @var UsersService
     */
    private $usersService;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * BlacklistVoter constructor.
     * @param SessionInterface      $session
     * @param ICacheService         $cacheService
     * @param RouterInterface       $router
     * @param IdentificationService $identificationService
     * @param string                $tokenName
     * @param UsersService          $usersService
     */
    public function __construct(
        ICacheService $cacheService,
        RouterInterface $router,
        IdentificationService $identificationService,
        string $tokenName,
        UsersService $usersService,
        LoggerInterface $logger


    )
    {
        $this->cacheService          = $cacheService;
        $this->router                = $router;
        $this->identificationService = $identificationService;
        $this->tokenName             = $tokenName;
        $this->usersService          = $usersService;
        $this->logger                = $logger;

    }

    public function checkIfSubscriptionRestricted(Request $request)
    {

        $session      = $request->getSession();
        $blockingTime = $session->get('subscription_not_allowed', false);
        if ($blockingTime && time() - strtotime($blockingTime) < self::TIME_LIMIT) {
            $session->set('subscription_not_allowed', date('Y-m-d H:i:s'));
            $this->logger->debug('Blocking time have not finished yet', [$blockingTime]);

            return $this->createNotAllowedResponse();
        }

        $sessionToken = $session->get($this->tokenName);

        if (empty($sessionToken)) {

            $this->logger->debug('No identification token was found. Subscription is not allowed');
            return $this->createNotAllowedResponse();
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
                $this->addToBlackList($sessionToken);
                return $this->createNotAllowedResponse();

            }

            $savedValue['updated_at'][] = date('Y-m-d H:i:s');
        }

        $this->cacheService->saveCache($sessionToken, $savedValue, self::TIME_LIMIT);
    }


    /**
     * @return RedirectResponse
     */
    private function createNotAllowedResponse()
    {
        $response = new RedirectResponse($this->router->generate('sub_not_allowed'));
        return $response;
    }

    /**
     * @param string $sessionToken
     */
    private function addToBlackList(string $sessionToken)
    {
        if (!empty($sessionToken)) {
            try {
                $userIdentity = $this->identificationService->getUserIdentityByTransactionToken($sessionToken);
                $this->usersService->addUserToBlackListByIdentity($userIdentity);
            } catch (\Exception $e) {
            }
        }
    }
}