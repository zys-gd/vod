<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_CacheProvider;
require_once dirname(__FILE__).'/CacheProviderInterface.php';

/**
 * Memcache cache provider may be used by the DeviceApi and DeviceApiWeb to cache the
 * detected properties. Using cache improves the performance by not doing
 * unnecessary lookups in the data files.
 *
 * @package Mobi\Mtld\DA\Device\CacheProvider
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_CacheProvider_MemCacheProvider implements Mobi_Mtld_DA_CacheProvider_CacheProviderInterface {
    
    private $memcache;
    private $cacheItemExpiry;

    /**
     * Construct a CacheProvider object.
     *
     * @param int    cacheItemExpiry=86400 number of seconds before a cache item is expired
     * @param string host=localhost memcache host
     * @param int    port=11211     memcache host port
     * @param bool   pconnect=false true=persistent connection
     */
    public function __construct($cacheItemExpiry=86400, $host='localhost', $port=11211, $pconnect=false) {
        $this->cacheItemExpiry = $cacheItemExpiry;
        $this->memcache = new Memcache();
        if ($pconnect) {
            // persistent connection
            if (!@$this->memcache->pconnect($host, $port)) {
                throw new Exception("Could open a persistent connection to memcache.");
            }
        } else {
            // normal connection
            if (!@$this->memcache->connect($host, $port)) {
                throw new Exception("Could open connection to memcache.");
            }
        }
    }

    /**
     * Get a stored value form cache.
     *
     * @param string key cache key
     * @return mixed the cached value or null if the key does not exist in cache
     */
    public function get($key){
        $value = $this->memcache->get($key);
        if ($value) {
            return unserialize($value);
        }
        return null;
    }

   /**
    * Store a value in cache.
    *
    * @param string key cache key
    * @value  mixed value value to be set to the cache key
    * @return bool true=cache stored 
    */
    public function set($key, $value){
        if (!$this->memcache->set($key, serialize($value), false, $this->cacheItemExpiry)) {
            return false;
        }
        return true;
    }

    /**
     * Remove an item from the cache.
     *
     * @param string key cache key
     * @return bool true=cache deleted
     */
    public function delete($key){
        return $this->memcache->delete($key);
    }

    /**
     * Clear the whole cache.
     * @return bool true=cache cleared
     */
    public function clear(){
        return $this->memcache->flush();
    }
}
