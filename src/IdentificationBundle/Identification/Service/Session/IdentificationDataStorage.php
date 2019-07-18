<?php

namespace IdentificationBundle\Identification\Service\Session;

use IdentificationBundle\Identification\Service\StorageInterface;

/**
 * Class IdentificationDataStorage
 */
class IdentificationDataStorage
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * IdentificationDataStorage constructor
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param int $carrierId
     */
    public function storeCarrierId(int $carrierId): void
    {
        $this->storage->storeValue('isp_detection_data', ['carrier_id' => $carrierId]);
    }

    /**
     * @return void
     */
    public function cleanCarrier(): void
    {
        $this->storage->cleanValue('isp_detection_data');
    }

    /**
     * @param string $token
     */
    public function setIdentificationToken(string $token): void
    {
        $identificationData = $this->getIdentificationData();
        $identificationData['identification_token'] = $token;

        $this->setIdentificationData($identificationData);
    }

    /**
     * @return string|null
     */
    public function getIdentificationToken(): ?string
    {
        $identificationData = $this->getIdentificationData();

        return isset($identificationData['identification_token']) ? $identificationData['identification_token'] : null;
    }

    /**
     * @return array
     */
    public function getIdentificationData(): array
    {
        return $this->storage->readValue('identification_data') ?? [];
    }

    /**
     * @param bool $value
     *
     * @return void
     */
    public function setSubscribeAfterIdent(bool $value = true): void
    {
        $this->storage->storeValue($this->key('subscribeAfterIdent'), $value);
    }

    /**
     * @return bool|null
     */
    public function getSubscribeAfterIdent(): ?bool
    {
        return $this->storage->readValue($this->key('subscribeAfterIdent'));
    }

    /**
     * @return string|null
     */
    public function getRedirectIdentToken(): ?string
    {
        return $this->storage->readValue($this->key('redirectIdent[token]'));
    }

    /**
     * @param string $token
     */
    public function setRedirectIdentToken(string $token): void
    {
        $this->storage->storeValue($this->key('redirectIdent[token]'), $token);
    }

    /**
     * @param string $token
     */
    public function setConsentFlowToken(string $token): void
    {
        $this->storage->storeValue($this->key('consentFlow[token]'), $token);
    }

    /**
     * @return string|null
     */
    public function getConsentFlowToken(): ?string
    {
        return $this->storage->readValue($this->key('consentFlow[token]'));
    }

    /**
     * @return bool
     */
    public function isPostPaidRestricted(): bool
    {
        return $this->storage->readValue($this->key('isPostPaidRestricted')) === 1;
    }

    /**
     * @param $value
     */
    public function setPostPaidRestricted($value): void
    {
        $this->storage->storeValue($this->key('isPostPaidRestricted'), $value);
    }

    /**
     * @return void
     */
    public function setAutoIdentAttempt(): void
    {
        $this->storage->storeValue($this->key('is_tried_to_autoident'), true);
    }

    /**
     * @return bool|null
     */
    public function getAutoIdentAttempt(): ?bool
    {
        return $this->storage->readValue($this->key('is_tried_to_autoident'));
    }

    /**
     * @param array $identificationData
     */
    private function setIdentificationData(array $identificationData): void
    {
        $this->storage->storeValue('identification_data', $identificationData);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function key(string $key): string
    {
        return SessionStorage::STORAGE_KEY . "[$key]";
    }
}