<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 15:20
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Entity\User;

class IdentificationStatus
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;

    /**
     * IdentificationStatus constructor.
     * @param IdentificationDataStorage $dataStorage
     */
    public function __construct(IdentificationDataStorage $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    public function isIdentified(): bool
    {
        $identificationData = $this->dataStorage->readIdentificationData();
        return isset($identificationData['identification_token']);
    }

    public function finishIdent(string $token, User $user): void
    {
        $this->dataStorage->storeIdentificationToken($token);
        $this->dataStorage->storeValue('is_wifi_flow', false);
    }

    public function isAlreadyTriedToAutoIdent(): bool
    {
        return (bool)$this->dataStorage->readValue('is_tried_to_autoident');
    }

    public function registerAutoIdentAttempt(): void
    {
        $this->dataStorage->storeValue('is_tried_to_autoident', true);
    }

    public function isWifiFlowStarted(): bool
    {
        return (bool)$this->dataStorage->readValue('is_wifi_flow');
    }

}