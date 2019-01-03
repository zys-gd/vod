<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_CacheProvider;
require_once dirname(__FILE__).'/CacheProviderInterface.php';

/**
 * APC cache provider may be used by the DeviceApi and DeviceApiWeb to cache the
 * detected properties. Using cache improves the performance by not doing
 * unnecessary lookups in the data files.
 *
 * @package Mobi\Mtld\DA\Device\CacheProvider
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_CacheProvider_ApcCacheProvider implements Mobi_Mtld_DA_CacheProvider_CacheProviderInterface {

    private $cacheItemExpiry;

    /**
     * Construct a CacheProvider object.
     *
     * @param int cacheItemExpiry=86400 number of seconds before a cache item is expired
     */
    public function __construct($cacheItemExpiry=86400) {
        $this->cacheItemExpiry = $cacheItemExpiry;
    }

    /**
     * Get a stored value form cache.
     *
     * @param string key cache key
     * @return mixed the cached value or null if the key does not exist in cache
     */
    public function get($key) {
        if (apc_exists($key)) {
            return unserialize(apc_fetch($key));
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
   public function set($key, $value) {
        if (!apc_store($key, serialize($value), $this->cacheItemExpiry)) {
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
    public function delete($key) {
        return apc_delete($key);
    }

    /**
     * Clear the whole cache.
     * @return bool true=cache cleared
     */
    public function clear() {
        return apc_clear_cache('user');
    }
}
