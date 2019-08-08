<?php

namespace IdentificationBundle\Identification\Service\Session;

use IdentificationBundle\Identification\Service\StorageInterface;

/**
 * Class IdentificationDataStorage
 */
class IdentificationDataStorage
{
    const AUTO_IDENT_ATTEMPT_KEY = 'is_tried_to_autoident';
    const CONSENT_FLOW_TOKEN_KEY = 'consentFlow[token]';
    const REDIRECT_IDENT_TOKEN_KEY = 'redirectIdent[token]';
    const POST_PAID_RESTRICTED_KEY = 'isPostPaidRestricted';
    const SUBSCRIBE_AFTER_IDENT_KEY = 'subscribeAfterIdent';

    const IDENTIFICATION_DATA_KEY = 'identification_data';
    const ISP_DETECTION_DATA_KEY = 'isp_detection_data';

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
     * @param string $key
     * @param $value
     */
    public function storeValue(string $key, $value): void
    {
        $this->storage->storeValue(SessionStorage::STORAGE_KEY . "[$key]", $value);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function readValue(string $key)
    {
        return $this->storage->readValue(SessionStorage::STORAGE_KEY . "[$key]");
    }

    /**
     * @param int $carrierId
     */
    public function setCarrierId(int $carrierId): void
    {
        $this->storage->storeValue(self::ISP_DETECTION_DATA_KEY, ['carrier_id' => $carrierId]);
    }

    /**
     * @return void
     */
    public function cleanCarrier(): void
    {
        $this->storage->cleanValue(self::ISP_DETECTION_DATA_KEY);
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

        return isset($identificationData['identification_token'])
            ? $identificationData['identification_token']
            : null;
    }

    /**
     * @return array
     */
    public function getIdentificationData(): array
    {
        return $this->storage->readValue(self::IDENTIFICATION_DATA_KEY) ?? [];
    }

    /**
     * @return string|null
     */
    public function getRedirectIdentToken(): ?string
    {
        return $this->readValue(self::REDIRECT_IDENT_TOKEN_KEY);
    }

    /**
     * @param string $token
     */
    public function setRedirectIdentToken(string $token): void
    {
        $this->storeValue(self::REDIRECT_IDENT_TOKEN_KEY, $token);
    }

    /**
     * @param array $identificationData
     */
    private function setIdentificationData(array $identificationData): void
    {
        $this->storage->storeValue(self::IDENTIFICATION_DATA_KEY, $identificationData);
    }
}