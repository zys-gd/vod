<?php

namespace SubscriptionBundle\Service;

use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
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
    /** @var UserRepository */
    private $userRepository;

    public function __construct(RouterInterface $router, LoggerInterface $logger, UserRepository $userRepository)
    {
        $this->router         = $router;
        $this->logger         = $logger;
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

        /** @var User $user */
        $user = $this->userRepository->findOneByIdentificationToken($identificationData['identification_token']);

        $this->logger->debug('Obtained user', [
            'userUuid' => $user->getUuid(),
            'msidsn'   => $user->getIdentifier()
        ]);

        return $user;
    }
}