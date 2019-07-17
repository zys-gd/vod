<?php

namespace IdentificationBundle\Identification\Service;

use IdentificationBundle\Identification\Service\Session\SessionStorageInterface;

/**
 * Class IdentificationDataStorage
 */
class IdentificationDataStorage
{
    /**
     * @var SessionStorageInterface
     */
    private $sessionStorage;

    /**
     * IdentificationDataStorage constructor
     *
     * @param SessionStorageInterface $sessionStorage
     */
    public function __construct(SessionStorageInterface $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * @param int $carrierId
     */
    public function storeCarrierId(int $carrierId): void
    {
        $this->sessionStorage->storeValue('isp_detection_data', ['carrier_id' => $carrierId]);
    }

    /**
     * @return void
     */
    public function cleanCarrier(): void
    {
        $this->sessionStorage->cleanValue('isp_detection_data');
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
     * @return array
     */
    public function getIdentificationData(): array
    {
        return $this->sessionStorage->readValue('identification_data') ?? [];
    }

    /**
     * @param bool $value
     *
     * @return void
     */
    public function setSubscribeAfterIdent(bool $value = true): void
    {
        $this->sessionStorage->storeStorageValue('subscribeAfterIdent', $value);
    }

    /**
     * @return bool|null
     */
    public function getSubscribeAfterIdent(): ?bool
    {
        return $this->sessionStorage->readStorageValue('subscribeAfterIdent');
    }

    /**
     * @return string|null
     */
    public function getRedirectIdentToken(): ?string
    {
        return $this->sessionStorage->readStorageValue('redirectIdent[token]');
    }

    /**
     * @param string $token
     */
    public function setRedirectIdentToken(string $token): void
    {
        $this->sessionStorage->storeStorageValue('redirectIdent[token]', $token);
    }

    /**
     * @param string $token
     */
    public function setConsentFlowToken(string $token): void
    {
        $this->sessionStorage->storeStorageValue('consentFlow[token]', $token);
    }

    /**
     * @return string|null
     */
    public function getConsentFlowToken(): ?string
    {
        return $this->sessionStorage->readStorageValue('consentFlow[token]');
    }

    /**
     * @return bool
     */
    public function isPostPaidRestricted(): bool
    {
        return $this->sessionStorage->readStorageValue('isPostPaidRestricted') === 1;
    }

    /**
     * @param $value
     */
    public function setPostPaidRestricted($value): void
    {
        $this->sessionStorage->storeStorageValue('isPostPaidRestricted', $value);
    }

    /**
     * @return void
     */
    public function setAutoIdentAttempt(): void
    {
        $this->sessionStorage->storeStorageValue('is_tried_to_autoident', true);
    }

    /**
     * @return bool|null
     */
    public function getAutoIdentAttempt(): ?bool
    {
        return $this->sessionStorage->readStorageValue('is_tried_to_autoident');
    }

    /**
     * @param array $identificationData
     */
    private function setIdentificationData(array $identificationData): void
    {
        $this->sessionStorage->storeValue('identification_data', $identificationData);
    }
}