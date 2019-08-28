<?php

namespace IdentificationBundle\WifiIdentification\Service;

use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use IdentificationBundle\Identification\Service\StorageInterface;

/**
 * Class WifiIdentificationDataStorage
 */
class WifiIdentificationDataStorage
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * WifiIdentificationDataStorage constructor
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param bool $isWifiFlow
     */
    public function setWifiFlow(bool $isWifiFlow): void
    {
        $this->storage->storeValue(SessionStorage::STORAGE_KEY . '[is_wifi_flow]', $isWifiFlow);
    }

    /**
     * @return bool|null
     */
    public function isWifiFlow(): ?bool
    {
        return $this->storage->readValue(SessionStorage::STORAGE_KEY . '[is_wifi_flow]');
    }

    /**
     * @param $result
     */
    public function setPinVerifyResult(PinVerifyResult $result): void
    {
        $this->storage->storeOperationResult('pinVerify', $result);
    }

    /**
     * @return PinVerifyResult|null
     */
    public function getPinVerifyResult(): ?PinVerifyResult
    {
        return $this->storage->readOperationResult('pinVerify');
    }

    /**
     * @param $result
     */
    public function setPinRequestResult(PinRequestResult $result): void
    {
        $this->storage->storeOperationResult('pinRequest', $result);
    }

    /**
     * @return PinVerifyResult|null
     */
    public function getPinRequestResult(): ?PinRequestResult
    {
        $result = $this->storage->readOperationResult('pinRequest');

        return $result
            ? $result
            : null;
    }

    /**
     * @return void
     */
    public function cleanPinRequestResult(): void
    {
        $this->storage->cleanOperationResult('pinRequest');
    }
}