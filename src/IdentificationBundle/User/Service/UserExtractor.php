<?php

namespace IdentificationBundle\User\Service;

use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param IdentificationData $identificationData
     *
     * @return User|null
     */
    public function getUserByIdentificationData(IdentificationData $identificationData): ?User
    {
        /** @var User $user */
        $user = $this->userRepository->findOneByIdentificationToken($identificationData->getIdentificationToken());

        try{
            $this->logger->debug('Obtained user', [
                'userUuid' => $user->getUuid(),
                'msidsn'   => $user->getIdentifier()
            ]);
        } catch (\Throwable $e) {

        }

        return $user;
    }

    /**
     * @param Request $request
     *
     * @return User|null
     */
    public function getUserFromRequest(Request $request): ?User
    {
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($request->getSession());
        $this->logger->debug('Retrieving user user from request', [
            'identificationToken' => $identificationToken
        ]);

        if (!$identificationToken) {
            return null;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneByIdentificationToken($identificationToken);

        if ($user) {

            $this->logger->debug('Obtained user', [
                'userUuid' => $user->getUuid(),
                'msidsn'   => $user->getIdentifier()
            ]);
        }

        return $user;
    }
}