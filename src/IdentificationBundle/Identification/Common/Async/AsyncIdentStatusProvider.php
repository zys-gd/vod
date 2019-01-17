<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 15:54
 */

namespace IdentificationBundle\Identification\Common\Async;


use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\UserRepository;

class AsyncIdentStatusProvider
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * AsyncIdentStatusProvider constructor.
     * @param IdentificationDataStorage $dataStorage
     * @param UserRepository            $userRepository
     */
    public function __construct(IdentificationDataStorage $dataStorage, UserRepository $userRepository)
    {
        $this->dataStorage    = $dataStorage;
        $this->userRepository = $userRepository;
    }

    /**
     * @return bool
     */
    public function isCallbackReceived(): bool
    {
        try {
            if (!$tempToken = $this->dataStorage->readValue('redirectIdent[token]')) {
                throw new FailedIdentificationException('Ident is not started');
            }

            $user = $this->userRepository->findOneByIdentificationToken($tempToken);
            return (bool)$user;
        } catch (\Exception $exception) {
            return false;
        }
    }
}