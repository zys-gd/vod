<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 14:00
 */

namespace IdentificationBundle\Identification\Common\Async;


use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Repository\UserRepository;

class AsyncIdentFinisher
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
     * @var IdentificationStatus
     */
    private $identificationStatus;
    /**
     * @var IdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var DataProvider
     */
    private $billingDataProvider;


    /**
     * AsyncIdentFinisher constructor.
     * @param IdentificationDataStorage     $dataStorage
     * @param UserRepository                $userRepository
     * @param IdentificationStatus          $identificationStatus
     * @param IdentificationHandlerProvider $handlerProvider
     * @param DataProvider                  $billingDataProvider
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        UserRepository $userRepository,
        IdentificationStatus $identificationStatus,
        IdentificationHandlerProvider $handlerProvider,
        DataProvider $billingDataProvider
    )
    {
        $this->dataStorage          = $dataStorage;
        $this->userRepository       = $userRepository;
        $this->identificationStatus = $identificationStatus;
        $this->handlerProvider      = $handlerProvider;
        $this->billingDataProvider  = $billingDataProvider;
    }

    /**
     * @throws FailedIdentificationException
     */
    public function finish(): void
    {
        if (!$tempToken = $this->dataStorage->getRedirectIdentToken()) {
            throw new FailedIdentificationException('Ident is not started');
        }

        if (!$user = $this->userRepository->findOneByIdentificationToken($tempToken)) {
            throw new FailedIdentificationException('Callback are not received yet');
        }

        $result = $this->billingDataProvider->getProcessData($user->getIdentificationProcessId());
        if (!$result->isSuccessful()) {
            throw  new FailedIdentificationException('Identification is not finished yet');
        }

        $this->identificationStatus->finishIdent($tempToken, $user);
    }
}