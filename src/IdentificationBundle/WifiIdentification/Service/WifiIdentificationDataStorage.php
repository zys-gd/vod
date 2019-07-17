<?php

namespace IdentificationBundle\WifiIdentification\Service;

use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Identification\Service\Session\SessionStorageInterface;

/**
 * Class WifiIdentificationDataStorage
 */
class WifiIdentificationDataStorage
{
    /**
     * @var SessionStorageInterface
     */
    private $sessionStorage;

    /**
     * WifiIdentificationDataStorage constructor
     *
     * @param SessionStorageInterface $sessionStorage
     */
    public function __construct(SessionStorageInterface $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * @param bool $isWifiFlow
     */
    public function setWifiFlow(bool $isWifiFlow): void
    {
        $this->sessionStorage->storeStorageValue('is_wifi_flow', $isWifiFlow);
    }

    /**
     * @return bool|null
     */
    public function isWifiFlow(): ?bool
    {
        return $this->sessionStorage->readStorageValue('is_wifi_flow');
    }

    /**
     * @param $result
     */
    public function setPinVerifyResult(PinVerifyResult $result): void
    {
        $this->sessionStorage->storeOperationResult('pinVerify', $result);
    }

    /**
     * @return PinVerifyResult|null
     */
    public function getPinVerifyResult(): ?PinVerifyResult
    {
        return $this->sessionStorage->readOperationResult('pinVerify');
    }

    /**
     * @param $result
     */
    public function setPinRequestResult(PinRequestResult $result): void
    {
        $this->sessionStorage->storeOperationResult('pinRequest', $result);
    }

    /**
     * @return PinVerifyResult|null
     */
    public function getPinRequestResult(): ?PinRequestResult
    {
        return $this->sessionStorage->readOperationResult('pinRequest');
    }

    /**
     * @return void
     */
    public function cleanPinRequestResult(): void
    {
        $this->sessionStorage->cleanOperationResult('pinRequest');
    }
}