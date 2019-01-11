<?php

namespace SubscriptionBundle\Service;

use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\Service\Action\Identification\Common\IdentificationFlowDataExtractor;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UserExtractor
{
    /** @var  RouterInterface */
    private $router;
    /** @var LoggerInterface */
    private $logger;
    /** @var UserRepository  */
    private $userRepository;

    public function __construct(RouterInterface $router, LoggerInterface $logger, UserRepository $userRepository)
    {
        $this->router = $router;
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     *
     * @return User
     */
    public function getUserFromRequest(Request $request): User
    {
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($request->getSession());
        $this->logger->debug('Retrieving user user from request', [
            'identificationData' => $identificationData
        ]);

        $identificationToken = $identificationData['identification_token'] ?? null;
        if (!$identificationToken) {

            $wrongOperatorUrl = $this->router->generate(
                'wrong_operator',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
            $this->logger->debug('No token id found. Redirecting via exception', ['redirectUrl' => $wrongOperatorUrl]);
            throw new RedirectRequiredException($wrongOperatorUrl);
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['identificationToken' => $identificationToken]);

        $this->logger->debug('Obtained user', [
            'userUuid' => $user->getUuid(),
            'msidsn' => $user->getIdentifier()
        ]);

        return $user;
    }
}