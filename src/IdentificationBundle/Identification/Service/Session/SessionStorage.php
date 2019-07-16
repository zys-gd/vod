<?php

namespace IdentificationBundle\Identification\Service\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SessionStorage
 */
class SessionStorage implements SessionStorageInterface
{
    const STORAGE_KEY = 'storage';
    const RESULTS_KEY = 'results';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * IdentificationDataStorage constructor
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function readStorageValue(string $key): ?string
    {
        return $this->session->get(self::STORAGE_KEY . "[$key]", '');
    }

    /**
     * @param string $key
     * @param $value
     */
    public function storeStorageValue(string $key, $value): void
    {
        $this->session->set(self::STORAGE_KEY . "[$key]", $value);
    }

    /**
     * @param string $key
     */
    public function cleanStorageValue(string $key): void
    {
        $this->session->remove(self::STORAGE_KEY . "[$key]");
    }

    /**
     * @param string $key
     * @param $result
     */
    public function storeOperationResult(string $key, $result): void
    {
        $this->session->set(self::RESULTS_KEY . "[$key]", serialize($result));
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function readOperationResult(string $key)
    {
        return unserialize($this->session->get(self::RESULTS_KEY . "[$key]"));
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function cleanOperationResult(string $key): void
    {
        $this->session->remove(self::RESULTS_KEY . "[$key]");
    }
}