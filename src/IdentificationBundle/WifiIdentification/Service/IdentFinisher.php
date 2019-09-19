<?php

namespace IdentificationBundle\WifiIdentification\Service;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use DeviceDetectionBundle\Service\Device;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;

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
     * @var \IdentificationBundle\User\Service\UserFactory
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
     * @param TokenGenerator                                 $tokenGenerator
     * @param \IdentificationBundle\User\Service\UserFactory $userFactory
     * @param IdentificationDataStorage                      $identificationDataStorage
     * @param EntityManagerInterface                         $entityManager
     */
    public function __construct(
        TokenGenerator $tokenGenerator,
        \IdentificationBundle\User\Service\UserFactory $userFactory,
        IdentificationDataStorage $identificationDataStorage,
        EntityManagerInterface $entityManager
    )
    {
        $this->tokenGenerator            = $tokenGenerator;
        $this->userFactory               = $userFactory;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->entityManager             = $entityManager;
    }

    /**
     * @param string           $msisdn
     * @param CarrierInterface $carrier
     * @param string           $ip
     *
     * @param DeviceData       $deviceData
     * @return User
     *
     * @throws \Exception
     */
    public function finish(string $msisdn, CarrierInterface $carrier, string $ip, DeviceData $deviceData): User
    {
        $token = $this->tokenGenerator->generateToken();
        $user  = $this->userFactory->create($msisdn, $carrier, $ip, $token, null, $deviceData);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->identificationDataStorage->setIdentificationToken($token);

        return $user;
    }

    /**
     * @param User   $user
     * @param string $msisdn
     * @param string $ip
     */
    public function finishForExistingUser(User $user, string $msisdn, string $ip, DeviceData $deviceData): void
    {
        $token = $this->tokenGenerator->generateToken();

        $user->setIdentifier($msisdn);
        $user->setIdentificationToken($token);
        $user->setIp($ip);

        $this->entityManager->persist($user);
        $this->entityManager->flush();


        $user->setDeviceManufacturer($deviceData->getDeviceManufacturer());
        $user->setDeviceModel($deviceData->getDeviceModel());
        $user->setConnectionType($deviceData->getConnectionType());
        $user->setIdentificationUrl($deviceData->getIdentificationUrl());
        $user->setLanguageCode($deviceData->getBrowserLanguage());


        $this->identificationDataStorage->setIdentificationToken($token);
    }
}