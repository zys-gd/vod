<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_CacheProvider;

/**
 * A cache provider such as APC or Memcache may be used by the DeviceApi and
 * DeviceApiWeb to cache the detected properties. Using cache improves the
 * performance by not doing unnecessary lookups in the data files. A valid cache
 * provider must implement this interface to be usable by the APIs.
 *
 * @package Mobi\Mtld\DA\Device\CacheProvider
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
interface Mobi_Mtld_DA_CacheProvider_CacheProviderInterface {
    /**
     * Get a stored value form cache.
     *
     * @param string key cache key
     * @return mixed the cached value or null if the key does not exist in cache
     */
    public function get($key);

    /**
     * Store a value in cache.
     *
     * @param string key cache key
     * @value mixed  value value to be set to the cache key
     */
    public function set($key, $value);
    
    /**
     * Remove an item from the cache.
     *
     * @param string key cache key
     */
    public function delete($key);
    
    /**
     * Clear the whole cache.
     */
    public function clear();
}
