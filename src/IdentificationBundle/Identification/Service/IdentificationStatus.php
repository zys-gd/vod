<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 15:20
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;

class IdentificationStatus
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;

    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;

    /**
     * IdentificationStatus constructor
     *
     * @param IdentificationDataStorage $dataStorage
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage
    ) {
        $this->dataStorage = $dataStorage;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
    }

    public function isIdentified(): bool
    {
        $identificationData = $this->dataStorage->getIdentificationData();
        return isset($identificationData['identification_token']);
    }

    public function finishIdent(string $token, User $user): void
    {
        $this->dataStorage->setIdentificationToken($token);
        $this->wifiIdentificationDataStorage->setWifiFlow(false);
    }

    public function isAlreadyTriedToAutoIdent(): bool
    {
        return (bool)$this->dataStorage->getAutoIdentAttempt();
    }

    public function registerAutoIdentAttempt(): void
    {
        $this->dataStorage->setAutoIdentAttempt();
    }

    public function isWifiFlowStarted(): bool
    {
        return (bool) $this->wifiIdentificationDataStorage->isWifiFlow();
    }

}