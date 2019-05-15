<?php

namespace IdentificationBundle\WifiIdentification\Service;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Identification\Service\UserFactory;

/**
 * Class IdentFinisher
 */
class IdentFinisher
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * IdentFinisher constructor.
     * @param TokenGenerator            $tokenGenerator
     * @param UserFactory               $userFactory
     * @param IdentificationDataStorage $identificationDataStorage
     * @param EntityManagerInterface    $entityManager
     */
    public function __construct(
        TokenGenerator $tokenGenerator,
        UserFactory $userFactory,
        IdentificationDataStorage $identificationDataStorage,
        EntityManagerInterface $entityManager
    ) {
        $this->tokenGenerator            = $tokenGenerator;
        $this->userFactory               = $userFactory;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->entityManager             = $entityManager;
    }

    /**
     * @param string $msisdn
     * @param CarrierInterface $carrier
     * @param string $ip
     *
     * @return User
     */
    public function finish(string $msisdn, CarrierInterface $carrier, string $ip): User
    {
        $token = $this->tokenGenerator->generateToken();
        $user  = $this->userFactory->create($msisdn, $carrier, $ip, $token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->identificationDataStorage->storeIdentificationToken($token);

        return $user;
    }

    /**
     * @param User $user
     * @param string $msisdn
     * @param string $ip
     */
    public function finishForExistingUser(User $user, string $msisdn, string $ip): void
    {
        $token = $this->tokenGenerator->generateToken();

        $user->setIdentifier($msisdn);
        $user->setIdentificationToken($token);
        $user->setIp($ip);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->identificationDataStorage->storeIdentificationToken($token);
    }
}