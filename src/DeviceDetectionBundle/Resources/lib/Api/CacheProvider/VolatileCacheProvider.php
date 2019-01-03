<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_CacheProvider;
require_once dirname(__FILE__).'/CacheProviderInterface.php';

/**
 * Volatile cache can be used by the DeviceApi class to cache the detected 
 * properties. Using cache improves the performance by not doing unnecessary 
 * lookups in the data files.
 * 
 * It is based on an associative array where the key is CRC3 hashed and the 
 * value can be any data type.
 * 
 * In a stateless web application, there is no point to use this cache provider.
 * It is mainly meant to client environments where repeated User-Agents are 
 * checked (e.g. processing a User-Agent list) and the cache provider can be
 * kept in memory across different calls to getProperties().
 *
 * @package Mobi\Mtld\DA\Device\CacheProvider
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_CacheProvider_VolatileCacheProvider implements Mobi_Mtld_DA_CacheProvider_CacheProviderInterface {
    
    /**
     * The default value to set the maximum number of cache entries we
     * can save. It must be integer, which means the maximum number of allowed 
     * entries is 2147483647. If value is 0, then cache is disabled.
     */
    const DEFAULT_MAX_CACHE_ENTRIES = 4096;
    
    private $volatileCache;
    private $maxCacheEntries = self::DEFAULT_MAX_CACHE_ENTRIES;
    private $cachedValuesCount;

    /**
     * Construct a CacheProvider object.
     * 
     */
    public function __construct() {
        $this->volatileCache = array();
        $this->cachedValuesCount = 0;
    }

    /**
     * Get a stored value form cache.
     *
     * @param string key cache key
     * @return mixed the cached value or null if the key does not exist in cache
     */
    public function get($key){
        $hashedKey = crc32($key);
        if (!isset($this->volatileCache[$hashedKey])) {
            return NULL;
        }
        return $this->volatileCache[$hashedKey];
    }

   /**
    * Store a value in cache.
    *
    * @param string key cache key
    * @value  mixed value value to be set to the cache key
    * @return bool true=cache stored 
    */
    public function set($key, $value){
        
        if (!$this->isEnabled()) {
            return FALSE;
        }
        
        $hashedKey = crc32($key);

        // If this is a new entry, increment counter
        if (!isset($this->volatileCache[$hashedKey])) {
            $this->cachedValuesCount++;
        }

        // Add it        
        $this->volatileCache[$hashedKey] = $value;
        
        // If we have exceeded the limit of cache entries,
        // reset the cache
        if ($this->cachedValuesCount > $this->maxCacheEntries) {            
            $this->volatileCache = array($hashedKey => $value);
            $this->cachedValuesCount = 1;
        }

        return TRUE;
    }

    /**
     * Remove an item from the cache.
     *
     * @param string key cache key
     * @return bool true=cache deleted
     */
    public function delete($key){
        $hashedKey = crc32($key);
        if (isset($this->volatileCache[$hashedKey])) {
            unset($this->volatileCache[$hashedKey]);
            $this->cachedValuesCount--;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Clear the whole cache.
     * @return bool true=cache cleared
     */
    public function clear(){
        $this->cachedValuesCount = 0;
        return $this->volatileCache = array();
    }

    /**
     * Set the max number of allowed entries in cache.
     * @param int $maxCacheEntries
     */
    public function setMaxCacheEntries($maxCacheEntries) {
        if (!is_int($maxCacheEntries)) {
            return;
        }
        if ($maxCacheEntries < 0) {
            $maxCacheEntries = 0;
        }
        $this->clear();
        $this->maxCacheEntries = $maxCacheEntries;
    }
    
    /**
     * Get the max number of allowed cache entries.
     * @return int
     */
    public function getMaxCacheEntries() {
        return $this->maxCacheEntries;
    }

    /**
     * Get number of entries
     * @return int
     */
    public function getNumCacheEntries() {
        return $this->cachedValuesCount;
    }
    
    /**
     * Return whether cache is enabled (more than zero allowed cache entries).
     * @return bool
     */
    public function isEnabled() {
        return $this->maxCacheEntries > 0;
    }
}
