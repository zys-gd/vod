<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 11:49
 */

namespace IdentificationBundle\Identification;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\UserRepository;

class IdentifierByUrl
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * IdentifierByUrl constructor.
     * @param UserRepository            $userRepository
     * @param IdentificationDataStorage $dataStorage
     * @param TokenGenerator            $generator
     * @param EntityManagerInterface    $entityManager
     */
    public function __construct(UserRepository $userRepository, IdentificationDataStorage $dataStorage, TokenGenerator $generator, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->dataStorage    = $dataStorage;
        $this->tokenGenerator = $generator;
        $this->entityManager  = $entityManager;
    }

    /**
     * @param string $urlId
     * @throws FailedIdentificationException
     */
    public function doIdentify(string $urlId): void
    {
        if (!$user = $this->userRepository->findOneByUrlId($urlId)) {
            throw new FailedIdentificationException(sprintf('urlId `%s` is not valid', $urlId));
        }

        $token = $user->getIdentificationToken();
        $this->dataStorage->storeIdentificationToken($token);
    }
}