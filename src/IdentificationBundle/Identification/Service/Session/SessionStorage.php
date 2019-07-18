<?php

namespace IdentificationBundle\Identification\Service\Session;

use IdentificationBundle\Identification\Service\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SessionStorage
 */
class SessionStorage implements StorageInterface
{
    const STORAGE_KEY = 'storage';
    const RESULTS_KEY = 'results';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * SessionStorage constructor
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
     * @return mixed
     */
    public function readValue(string $key)
    {
        return $this->session->get($key);
    }

    /**
     * @param string $key
     * @param $value
     */
    public function storeValue(string $key, $value): void
    {
        $this->session->set($key, $value);
    }

    /**
     * @param string $key
     */
    public function cleanValue(string $key): void
    {
        $this->session->remove($key);
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