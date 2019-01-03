<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_CacheProvider;
require_once dirname(__FILE__).'/CacheProviderInterface.php';

/**
 * File cache provider may be used by the DeviceApi and DeviceApiWeb to cache the
 * detected properties. Using cache improves the performance by not doing
 * unnecessary lookups in the data files.
 *
 * @package Mobi\Mtld\DA\Device\CacheProvider
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_CacheProvider_FileCacheProvider implements Mobi_Mtld_DA_CacheProvider_CacheProviderInterface {

    private $cacheDir;        // uses sys_get_temp_dir() by default
    private $cacheItemExpiry; // number of seconds

    /**
     * Construct a CacheProvider object.
     *
     * @param int cacheItemExpiry=86400 number of seconds before a cache item is expired
     * @param string cacheDir=system-temp-dir temp directory to place the cached files in
     */
    public function __construct($cacheItemExpiry=86400, $cacheDir=null) {
        $this->cacheItemExpiry = $cacheItemExpiry;
        $this->cacheDir = rtrim($cacheDir? $cacheDir: sys_get_temp_dir(), '/\\').
            '/'.__CLASS__.'/';
    }

    /**
     * Get a stored value form cache.
     *
     * @param string key cache key
     * @return mixed the cached value or null if the key does not exist in cache
     */
    public function get($key) {
        $path = $this->cachePath($key);
        if (file_exists($path)) {
            $mtime = @filemtime($path);
            if ($mtime + $this->cacheItemExpiry > time()) {
                return unserialize(@file_get_contents($path));
            }
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
        $path = $this->cachePath($key, true);
        if (@file_put_contents($path, serialize($value), LOCK_EX) === false) {
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
        $path = $this->cachePath($key);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Clear the whole cache.
     * @return bool true=cache cleared
     */
    public function clear() {
        if (file_exists($this->cacheDir)) {
            $this->rmdir($this->cacheDir, true);
        }
    }
    
    private function rmdir($dir, $empty=false) {
        // open the directory
        $handle = opendir($dir);
        while (false !== ($item = readdir($handle))) {
            if ($item != '.' && $item != '..') {
                $path = $dir.'/'.$item;
                if (is_dir($path)){
                    $this->rmdir($path);
                } else {
                    // check filename
                    if (preg_match("/\.da$/", $path)) {
                        @unlink($path);
                    }
                }
            }
        }

        // close the directory
        closedir($handle);

        if (!$empty) {
            @rmdir($dir);
        }
    }

    private function cachePath($key, $createDirectory=false) {
        $path = $this->cacheDir;
        // subdirectory
        $fragLevel        = 2;
        $fragSubDirLength = 2;
        for ($i = 0, $n = $fragLevel * $fragSubDirLength; $i < $n; $i += $fragSubDirLength){
            $path .= substr($key, $i, $fragSubDirLength).'/';
        }

        if ($createDirectory && !is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        return $path.substr($key, $fragLevel*$fragSubDirLength).'.da';
    }
}
