<?php

namespace IdentificationBundle\Identification\Service;

/**
 * Interface SessionStorageInterface
 */
interface StorageInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function readValue(string $key);

    /**
     * @param string $key
     * @param $value
     */
    public function storeValue(string $key, $value): void;

    /**
     * @param string $key
     */
    public function cleanValue(string $key): void;

    /**
     * @param string $key
     * @param string $result
     */
    public function storeOperationResult(string $key, string $result): void;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function readOperationResult(string $key);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function cleanOperationResult(string $key);
}