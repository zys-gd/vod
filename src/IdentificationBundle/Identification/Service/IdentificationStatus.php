<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 15:20
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
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
        return (bool) $this->dataStorage->getIdentificationToken();
    }

    public function finishIdent(string $token, User $user): void
    {
        $this->dataStorage->setIdentificationToken($token);
        $this->wifiIdentificationDataStorage->setWifiFlow(false);
    }

    public function isAlreadyTriedToAutoIdent(): bool
    {
        return (bool)$this->dataStorage->getFromStorage(IdentificationDataStorage::AUTO_IDENT_ATTEMPT_KEY);
    }

    public function registerAutoIdentAttempt(): void
    {
        $this->dataStorage->setToStorage(IdentificationDataStorage::AUTO_IDENT_ATTEMPT_KEY, true);
    }

    public function isWifiFlowStarted(): bool
    {
        return (bool) $this->wifiIdentificationDataStorage->isWifiFlow();
    }

    public function startWifiFlow(): void
    {
        $this->wifiIdentificationDataStorage->setWifiFlow(true);
    }
}