<?php

namespace IdentificationBundle\Identification\Service;

use IdentificationBundle\Identification\Service\Session\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class IdentificationDataStorage
 */
class IdentificationDataStorage
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SessionStorageInterface
     */
    private $sessionStorage;

    /**
     * IdentificationDataStorage constructor
     *
     * @param SessionInterface $session
     * @param SessionStorageInterface $sessionStorage
     */
    public function __construct(SessionInterface $session, SessionStorageInterface $sessionStorage)
    {
        $this->session = $session;
        $this->session = $sessionStorage;
    }

    public function storeOperationResult(string $key, $result): void
    {
        $this->session->set("results[$key]", serialize($result));
    }

    public function readPreviousOperationResult(string $key)
    {
        return unserialize($this->session->get("results[$key]"));
    }

    public function cleanPreviousOperationResult(string $key): void
    {
        $this->session->remove("results[$key]");
    }


    public function storeIdentificationToken(string $token): void
    {
        $identificationData = $this->readIdentificationData();

        $identificationData['identification_token'] = $token;

        $this->setIdentificationData($identificationData);
    }

    public function readIdentificationData(): array
    {
        if ($this->session->has('identification_data')) {
            $identificationData = $this->session->get('identification_data');
        } else {
            $identificationData = [];
        }
        return $identificationData;
    }

    private function setIdentificationData(array $identificationData): void
    {
        $this->session->set('identification_data', $identificationData);
    }

    public function storeCarrierId(int $carrierId): void
    {
        $this->session->set('isp_detection_data', ['carrier_id' => $carrierId]);
    }

    public function cleanCarrier(): void
    {
        $this->session->remove('isp_detection_data');
    }

    public function storeIsClickableSubImage(bool $flag): void
    {
        // todo rework after task with LP
        //$this->storeValue('is_clickable_sub_image', $flag);
    }

    /**
     * @param $result
     */
    public function setPinVerifyResult($result): void
    {
        $this->sessionStorage->storeOperationResult('pinVerify', $result);
    }

    /**
     * @return mixed
     */
    public function getPinVerifyResult()
    {
        return $this->sessionStorage->readOperationResult('pinVerify');
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
     * @return bool
     */
    public function isWifiFlow(): bool
    {
        return $this->sessionStorage->readStorageValue('is_wifi_flow');
    }

    /**
     * @param bool $isWifiFlow
     */
    public function setWifiFlow(bool $isWifiFlow): void
    {
        $this->sessionStorage->storeStorageValue('is_wifi_flow', $isWifiFlow);
    }
}