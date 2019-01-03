<?php
/**
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;
require_once dirname(__FILE__).'/../CacheProvider/CacheProviderInterface.php';

/**
 * DeviceApi Configurations. To change the default configs of the DeviceAtlas
 * DeviceApi create an instance of this class, set your preferred configs and
 * pass the instance to the constructor of a DeviceApi or DeviceApiWeb. Note
 * that you may change the configs even after the Config instance has been
 * passed to the DeviceApi.
 *
 * @package Mobi\Mtld\DA\Device
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Device_Config {
    /**
     * The default cookie name that the client side properties would be set to.
     * When using getProperties() in a web application, the detection would
     * automatically use the contents of this cookie if it exists. If you want
     * the client side properties to be used add the DeviceAtlas client
     * side component (JS lib) to your web-site pages.
     */
    const DEFAULT_COOKIE_NAME        = "DAPROPS";
    /**
     * The default config value for getProperties() to include User-Agent dynamic
     * properties.
     */
    const DEFAULT_INCLUDE_UA_PROPS   = true;
    /**
     * The default config value for getProperties(HEADERS) or getProperties(REQUEST)
     * to check the Accept-Language header and include properties to the property
     * set for getting client's language and locale preferences.
     */
    const DEFAULT_INCLUDE_LANG_PROPS = true;
    /**
     * The default config value for getProperties() to include the matched and
     * unmatched parts of the User-Agent to the property set.
     */
    const DEFAULT_INCLUDE_MATCH_INFO = false;
    /**
     * The default config value for getProperties() return value
     * when there was no properties. true = if no properties return null, false =
     * if no property return an instance of Properties with no Property inside it.
     */
    const DEFAULT_RETURN_NULL_WHEN_NO_PROPERTIES = false;
    
    private $cookieName                 = self::DEFAULT_COOKIE_NAME;
    private $includeUaProps             = self::DEFAULT_INCLUDE_UA_PROPS;
    private $includeLangProps           = self::DEFAULT_INCLUDE_LANG_PROPS;
    private $includeMatchInfo           = self::DEFAULT_INCLUDE_MATCH_INFO;
    private $returnNullWhenNoProperties = self::DEFAULT_RETURN_NULL_WHEN_NO_PROPERTIES;

    private $useTreeOptimizer           = false;
    private $ignoreDataFileChanges      = false;
    private $cacheProvider;
    private $optimizerTempDir;

    /**
     * To change the cookie name that the client side properties would be set to.
     * When using getProperties() in a web application, the detection would
     * automatically use the contents of this cookie if it exists. If you want
     * the client side properties to be used add the DeviceAtlas client side
     * component (JS lib) to your web-site pages.<br/>
     * When the user-agent or HTTP headers are set manually to getProperties()
     * the client side properties (probably the value set to the cookie) can be
     * manually passed to this methods as the second argument, note that this
     * config is only used for getProperties(REQUEST-OBJECT). If you set the
     * cookie-name to null then the client-side properties cookie will be ignored.
     *
     * @param string cookieName The cookie name which the client side properties
     *        are set to. Set to null to turn off using client-side properties
     */
    public function setCookieName($cookieName) {
        $this->cookieName = $cookieName;
    }
    /**
     * Get the cookie name that the client side properties would be set to.
     * When using getProperties() in a web application, the detection would
     * automatically use the contents of this cookie if it exists. If you want
     * the client side properties to be used add the DeviceAtlas client side
     * component (JS lib) to your web-site pages.<br/>
     * When the user-agent or HTTP headers are set manually to getProperties()
     * the client side properties (probably the value set to the cookie)
     * can be manually passed to this methods as the second argument, note that
     * this config is only used for getProperties(REQUEST-OBJECT).
     *
     * @return string The cookie name which the client side properties are set to
     */
    public function getCookieName() {
        return $this->cookieName;
    }

    /**
     * To disable or enable getProperties(HEADERS) or getProperties(REQUEST)
     * including language and locale properties. To check the Accept-Language
     * header and include properties to the property set for getting client's
     * language and locale preferences set to true.
     * If you do not use this properties you can set this config to false to
     * make the detection marginally faster.
     *
     * @param bool includeUaProps true=include ua properties
     */
    public function setIncludeUaProps($includeUaProps) {
        $this->includeUaProps = $includeUaProps;
    }
    /**
     * Get if the User-Agent properties are being included in the property set or not.
     *
     * @return boolean true=include ua properties
     */
    public function getIncludeUaProps() {
        return $this->includeUaProps;
    }

    /**
     * To disable or enable getProperties(HEADERS) or getProperties(REQUEST)
     * including language and locale properties. To check the Accept-Language
     * header and include properties to the property set for getting client's
     * language and locale preferences set to true.
     * If you do not use this properties you can set this config to false to
     * make the detection marginally faster.
     *
     * @param bool includeLangProps true=include lang properties
     */
    public function setIncludeLangProps($includeLangProps) {
        $this->includeLangProps = $includeLangProps;
    }
    /**
     * Get if the language/locale properties are being included in the property set or not.
     *
     * @return bool true=include lang properties
     */
    public function getIncludeLangProps() {
        return $this->includeLangProps;
    }

    /**
     * To disable or enable getProperties() including the matched and unmatched
     * parts of the User-Agent to the property set.
     *
     * @param bool includeMatchInfo true=include match info properties
     */
    public function setIncludeMatchInfo($includeMatchInfo) {
        $this->includeMatchInfo = $includeMatchInfo;
    }
    /**
     * To get if getProperties() include the matched and unmatched parts of the
     * User-Agent to the property set.
     *
     * @return bool true=include match info properties
     */
    public function getIncludeMatchInfo() {
        return $this->includeMatchInfo;
    }

    /**
     * Set if the value returned by the getProperties() method should be null when 
     * there are no properties to return or whether an empty Properties object
     * should be returned instead.
     *
     * @param bool returnNullWhenNoProperties true = return null instead of empty array
     */
    public function setReturnNullWhenNoProperties($returnNullWhenNoProperties) {
        $this->returnNullWhenNoProperties = $returnNullWhenNoProperties;
    }
    /**
     * Get if the value returned by getProperties() should be null when there are
     * no properties to return or an instance of Properties that has no Property
     * inside it should be returned.
     *
     * @return bool true=return null instead of empty array
     */
    public function getReturnNullWhenNoProperties() {
        return $this->returnNullWhenNoProperties;
    }

    /**
     * To optimize data. Optimizing the data file will dramatically reduce the
     * memory foot print and data file loading speed. It is highly recommend to
     * turn this config on when detecting devices in real-time using DeviceAtlasWeb.<br/>
     * When this config is on, the API will automatically divide the data file into
     * smaller pieces and cache them on the disk, the cached data will be used for
     * lookups afterwards. The API detects data file updates and will update the cache.
     *
     * @param bool useTreeOptimizer true=turn on data optimizer (by default this config
     *        is off for DeviceApi but on for DeviceApiWeb)
     */
    public function setUseTreeOptimizer($useTreeOptimizer) {
        $this->useTreeOptimizer = $useTreeOptimizer;
    }
    /**
     * To get data optimization status. Optimizing the data file will dramatically reduce the
     * memory foot print and data file loading speed. It is highly recommend to
     * turn this config on when detecting devices in real-time using DeviceAtlasWeb.<br/>
     * When this config is on, the API will automatically divide the data file into
     * smaller pieces and cache them on the disk, the cached data will be used for
     * lookups afterwards. The API detects data file updates and will update the cache.
     *
     * @return bool true=data optimizer is on
     */
    public function getUseTreeOptimizer() {
        return $this->useTreeOptimizer;
    }

    /**
     * When optimizer is turned on, the cached files will be put inside the system
     * directory by default, you can change the default optimizer temp/cache directory
     * with this method.
     *
     * @param string optimizerTempDir path to temp directory
     */
    public function setOptimizerTempDir($optimizerTempDir) {
        $optimizerTempDir = rtrim(rtrim($optimizerTempDir, '/'), '\\').'/';
        $this->optimizerTempDir = $optimizerTempDir;
    }
    /**
     * Get the temp directory path in which the optimizer puts the cached files.
     *
     * @return string path to the optimizer temp directory
     */
    public function getOptimizerTempDir() {
        return $this->optimizerTempDir? $this->optimizerTempDir: rtrim(rtrim(sys_get_temp_dir(), '/'), '\\').'/';
    }

    /**
     * Set a cache provider to the API to cache the lookup results. This will dramatically
     * increase the lookup performance in most situations. It is highly recommend to
     * use this config when detecting devices in real-time using DeviceAtlasWeb.
     *
     * @param Mobi_Mtld_DA_CacheProvider_CacheProviderInterface cacheProvider a cache provider instance
     */
    public function setCacheProvider(Mobi_Mtld_DA_CacheProvider_CacheProviderInterface $cacheProvider) {
        $this->cacheProvider = $cacheProvider;
    }
    /**
     * Get the cache provider instance being used by the API to cache the lookup results.
     * null is returned when a cache provider is not used.
     *
     * @return Mobi_Mtld_DA_CacheProvider_CacheProviderInterface cacheProvider a cache provider instance or null
     */
    public function getCacheProvider() {
        return $this->cacheProvider;
    }

    /**
     * When data file optimizer is set to true using the "setUseTreeOptimizer()" config, the API
     * will try to use a batch of optimized cached files instead of the original data file, the API
     * will do several checks to pick between the cached data file or the passed data file. While
     * this makes the API automatically sense and update the cached files. However if you manually
     * update the optimizer caches using the provided CLI tool (ExtraTools/DeviceApi/data-file-optimizer.php)
     * the checking is redundant and unnecessary, using this setting you can turn this checking off.
     *
     * @param bool ignoreDataFileChanges true=include ua properties
     */
    public function setIgnoreDataFileChanges($ignoreDataFileChanges) {
        $this->ignoreDataFileChanges = $ignoreDataFileChanges;
    }
    /**
     * Get if the data file optimizer should do data file update automatic checking.
     *
     * @return boolean true=include ua properties
     */
    public function getIgnoreDataFileChanges() {
        return $this->ignoreDataFileChanges;
    }

}
