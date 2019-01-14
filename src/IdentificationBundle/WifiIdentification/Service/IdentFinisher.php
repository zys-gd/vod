<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:53
 */

namespace IdentificationBundle\WifiIdentification\Service;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Identification\Service\UserFactory;

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
    public function __construct(TokenGenerator $tokenGenerator, UserFactory $userFactory, IdentificationDataStorage $identificationDataStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenGenerator            = $tokenGenerator;
        $this->userFactory               = $userFactory;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->entityManager             = $entityManager;
    }

    public function finish(string $msisdn, CarrierInterface $carrier, string $ip)
    {

        $token = $this->tokenGenerator->generateToken();
        $user  = $this->userFactory->create($msisdn, $carrier, $ip, $token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->identificationDataStorage->storeIdentificationToken($token);
    }
}