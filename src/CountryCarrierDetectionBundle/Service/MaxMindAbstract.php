<?php

namespace CountryCarrierDetectionBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use MaxMind\Db\Reader;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class MaxMindAbstract
 * @package CountryCarrierDetectionBundle\Service
 */
abstract class MaxMindAbstract
{
    /**
     * The IP service layer for getting the IP address of the current request.
     *
     * @var IpService
     */
    protected $ipService = null;
    /**
     * MaxMind internal database reader for performing IP lookup's.
     *
     * @var Reader
     */
    protected $reader = null;
    /**
     * Symfony logging errors/messages layer
     *
     * @var Logger
     */
    private $logger = null;

    /**
     * Symfony internal caching layer/wrapper
     *
     * @var AbstractAdapter
     */
    protected $cache = null;

    /**
     * Cache instances, for using for both save and get operations.
     *
     * @var array
     */
    protected static $cacheItems = array();

    /**
     * MaxMindAbstract constructor.
     * @param IpService $ipService
     * @param string $dbPath
     * @param AbstractAdapter $cache
     * @param Logger $logger
     */
    public function __construct(IpService $ipService, $dbPath, AdapterInterface $cache, Logger $logger)
    {
        $this->ipService = $ipService;
        if(file_exists($dbPath)){
            $this->reader    = new Reader($dbPath);
        }
        $this->cache     = $cache;
        $this->logger    = $logger;
    }

    /**
     * Fetch data from a MaxMind database file regarding the current request IP address.
     * Each type of databases (connection type, country and carrier) contains different indexes for the returned array.
     *
     * @return array
     */
    protected function read()
    {
        return $this->reader->get($this->ipService->getIp());
    }

    /**
     * Fetch data from a MaxMind database file regarding the IP address provided.
     * Each type of databases (connection type, country and carrier) contains different indexes for the returned array.
     *
     * @param string $ipAddress
     * @return array
     */
    protected function readByIp($ipAddress)
    {
        return $this->reader->get($ipAddress);
    }

    /**
     * Log an Error through Symfony Monolog logging service.
     *
     * @param string $message
     * @param string $serviceUsed Service can be either Country, Carrier or Connection Type
     * @param string $ipAddressSource Current request IP or custom IP address
     */
    protected function riseError($message, $serviceUsed, $ipAddressSource)
    {
        $context = array(
            'service_used' => $serviceUsed,
            'ip_address' => $ipAddressSource
        );

        $this->logger->error('MaxMind Service Error: ' . $message, $context);
    }

    /**
     * Log a Critical Error through Symfony Monolog logging service.
     *
     * @param string $message
     * @param string $serviceUsed Service can be either Country, Carrier or Connection Type
     * @param string $ipAddressSource Current request IP or custom IP address
     */
    protected function riseCritical($message, $serviceUsed, $ipAddressSource)
    {
        $context = array(
            'service_used' => $serviceUsed,
            'ip_address' => $ipAddressSource
        );

        $this->logger->critical('MaxMind Service Critical Error: ' . $message, $context);
    }

    /**
     * Log a Warning through Symfony Monolog logging service.
     *
     * @param string $message
     * @param string $serviceUsed Service can be either Country, Carrier or Connection Type
     * @param string $ipAddressSource Current request IP or custom IP address
     */
    protected function riseWarning($message, $serviceUsed, $ipAddressSource)
    {
        $context = array(
            'service_used' => $serviceUsed,
            'ip_address' => $ipAddressSource
        );

        $this->logger->warning('MaxMind Service Warning: ' . $message, $context);
    }

    /**
     * Create a unique key for caching the value for a service and an IP address.
     *
     * @param string $ipAddress
     * @param string $service
     * @return string
     */
    protected function generateCacheKey($ipAddress, $service)
    {
        return 'ip_' . ip2long($ipAddress) . '_' . $service;
    }

    /**
     * Return the value from cache associated with $key key.
     * If $value is not empty, it will also save the $value value into $key key from cache.
     * If there's no value at $key key, it will return null.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function getAndSaveCache($key, $value = null)
    {
        // create a globally-available cache instance for this key
        if (!isset(self::$cacheItems[$key]) || empty(self::$cacheItems[$key])) {
            // fetch this location from cache, based on $key
            self::$cacheItems[$key] = $this->cache->getItem($key);
        }

        // check for saving operation
        if (!empty($value)) {
            self::$cacheItems[$key]->set($value);
            $this->cache->save(self::$cacheItems[$key]);
        }

        // is there any saved information in cache?
        // if so, use it instead.
        if (self::$cacheItems[$key]->isHit()) {
            return self::$cacheItems[$key]->get();
        }

        return null;
    }

    /**
     * Remove an item from cache, based on key
     *
     * @param $key
     */
    protected function removeCache($key)
    {
        $this->cache->deleteItem($key);
    }

    /**
     * Return the Country/Carrier/Connection Type for the current request IP address.
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @return mixed
     */
    abstract public function get();

    /**
     * Return the Country/Carrier/Connection Type for the provided IP address.
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @param string $ipAddress
     * @return mixed
     */
    abstract public function getByIp($ipAddress);
}