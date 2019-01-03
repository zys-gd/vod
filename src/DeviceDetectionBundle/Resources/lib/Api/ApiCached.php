<?php
/*
 * package Mobi\Mtld\DA
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA (deprecated);
$path = dirname(__FILE__).'/';

require_once $path.'Device/DeviceApi.php';
require_once $path.'CacheProvider/ApcCacheProvider.php';
require_once $path.'CacheProvider/FileCacheProvider.php';
require_once $path.'CacheProvider/MemCacheProvider.php';
require_once $path.'/Device/TreeOptimized.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/InvalidPropertyException.php';

/**
 * This class significantly improves core DA PHP Api performance
 * when used on a standard website with a single detection per request.
 * 
 * Current optimizations consists of two main parts:
 * 
 * 1) tree optimizer - speeds up loading the json file<br/>
 * 2) cache provider - caches the results
 * 
 * <b>Typical usage:</b>
 * 
 * <pre>
 * $ua = $_SERVER['HTTP_USER_AGENT'];
 * 
 * $da_cache_provider = new Mobi_Mtld_DA_CacheProvider_FileCacheProvider();
 * $da_api_cached = new Mobi_Mtld_DA_ApiCached("json/sample.json",
 *												$da_cache_provider);
 * 
 * $properties = $da_api_cached->getProperties($ua);
 * </pre>
 * 
 * Client side properties
 * 
 * Client side properties can be collected and merged into the results by
 * using the DeviceAtlas Javascript detection file. The results from the client
 * side are sent to the server inside a cookie. The contents of this cookie can
 * be passed to the DeviceAtlas getProperty and getProperties methods. The 
 * client side properties over-ride any data file properties and also serve as
 * an input into additional logic to determine other properties such as the 
 * iPhone models that are otherwise not detectable. The following code shows
 * how this can be done:
 * 
 * <pre>
 * $ua = $_SERVER['HTTP_USER_AGENT'];
 * 
 * // Get the cookie containing the client side properties
 * $cookie_contents = null;
 * if (isset($_COOKIE['DAPROPS'])){
 *   $cookie_contents = $_COOKIE['DAPROPS'];
 * }
 * 
 * $da_cache_provider = new Mobi_Mtld_DA_CacheProvider_FileCacheProvider();
 * $da_api_cached = new Mobi_Mtld_DA_ApiCached("json/sample.json",
 *												$da_cache_provider);
 * 
 * $properties = $da_api_cached->getProperties($ua, $cookie_contents);
 * </pre>
 *  
 * <b>Note:</b>
 * 
 * It is not recommended to use ApiCached extension for batch processing
 * (i.e. multiple User-Agent detections during a single request). In these
 * situations use standard API interface.
 * 
 * See Api.php for more information
 * 
 *
 * @author Afilias Technologies Ltd
 * @deprecated This API is deprecated, it is highly recommended to use the new {@link Mobi_Mtld_DA_Device_DeviceApi DeviceApi} instead.
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_ApiCached {

    private $deviceApi;
    private $config;
    private $pathToJson;

	/**
	 * Creates new instance of Mobi_Mtld_DA_ApiCached object
	 *
	 * @param string $pathToJson The location of the file to read in.
	 * @param Mobi_Mtld_DA_CacheProvider_CacheProviderInterface $cacheProvider
	 * @param boolean $useTreeOptimizer
	 * @param string $treeOptimizerCacheDir Cache directory for the tree optimizer; uses sys_get_temp_dir() by default
	 * @param boolean $includeChangeableUserAgentProperties Also detect changeable user-agent properties
	 */
	public function __construct(
        $pathToJson,
        Mobi_Mtld_DA_CacheProvider_CacheProviderInterface $cacheProvider = null,
        $useTreeOptimizer = true,
        $treeOptimizerCacheDir = null,
        $includeChangeableUserAgentProperties = true) {

        $this->pathToJson = $pathToJson;

        $config = new Mobi_Mtld_DA_Device_Config();
        $config->setUseTreeOptimizer($useTreeOptimizer);
        if ($treeOptimizerCacheDir) {
            $config->setOptimizerTempDir($treeOptimizerCacheDir);
        }
        if (!$includeChangeableUserAgentProperties) {
            $config->setIncludeUaProps(false);
        }
        if ($cacheProvider) {
            $config->setCacheProvider($cacheProvider);
        }

        $this->config = $config;

        $this->deviceApi = new Mobi_Mtld_DA_Device_DeviceApi($config);
        $this->deviceApi->loadDataFromFile($pathToJson);
	}
	
	/**
	 * Returns an array of known properties (as strings) for the user agent
	 * 
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return array properties Property name => Property value
	 * 
	 * @throws Mobi_Mtld_DA_Exception_JsonException
	 */
	public function getProperties($userAgent, $cookie = null) {
		if ($userAgent === '') {
			return array();
		}

        $propsOut = array();
        foreach ($this->deviceApi->getProperties($userAgent, $cookie) as $name => $prop) {
            $propsOut[$name] = $prop->value();
        }
        return $propsOut;
	}

	/**
	 * Returns a value for the named property for this user agent
	 * 
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string $property The name of the property to return
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return string property
	 * 
	 * @throws Mobi_Mtld_DA_Exception_UnknownPropertyException
	 * @throws Mobi_Mtld_DA_Exception_InvalidPropertyException
	 * @throws Mobi_Mtld_DA_Exception_JsonException 
	 */
	public function getProperty($userAgent, $property, $cookie = null) {
		$properties = $this->getProperties($userAgent, $cookie);
		if (!isset($properties[$property])) {
			throw new Mobi_Mtld_DA_Exception_InvalidPropertyException("The property \"" . $property . "\" does not exist for the User Agent:\"" . $userAgent . "\"");
		}
		return $properties[$property];
	}

	/**
	 * DEPRECATED, not functional
	 * 
	 * @param boolean $useSysTempDir
	 */
	public function setUseSysTempDir($useSysTempDir){
		$this->useSysTempDir = (bool) $useSysTempDir;
	}

	/**
	 * Deletes all TreeOptimizer cache files
	 */
	public function clearTreeOptimizerCache(){
        $tree = new Mobi_Mtld_DA_Device_TreeOptimized($this->config->getOptimizerTempDir());
        $tree->clearCache();
	}

	/**
	 * Automatically populate full TreeOptimizer cache
	 */
	public function populateTreeOptimizerCache($force = false){
        $tree = new Mobi_Mtld_DA_Device_TreeOptimized($this->config->getOptimizerTempDir());
        $tree->populateCache($this->pathToJson, $force);
	}
}
