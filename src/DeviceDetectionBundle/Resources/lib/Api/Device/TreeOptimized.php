<?php
/*
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;
require_once Mobi_Mtld_DA_DEVICE_API_PATH.'Tree.php';

/**
 * This class is used by the main API class and should not be used directly.
 * 
 * @package Mobi\Mtld\DA\Device
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Device_TreeOptimized extends Mobi_Mtld_DA_Device_Tree {

    /**
     * Name of the file that stores the cached data file's file-size and file-modification-time.
     * This info is used to sense auto data file changes.
     */
    const CACHED_JSON_INFO_FILE_NAME    = 'da-cached-info';
    /** Name of the file that keeps a history of the cached data file directories. */
    const CACHED_JSON_HISTORY_FILE_NAME = 'da-cache-history';

    /** the directory in which the the api puts it's temp and cache files */
    protected $jsonTempDir;
    /** the directory in which the json fragments are put */
    protected $jsonBatchDir;

    /** batch config */
    private static $MIN_BATCH_SIZE = 256;
    /** max number of JSON files to keep in cache, older JSON files will be deleted. */
    private static $MAX_CACHE_JSON_FILES = 2;
    /** tree 'v' node */
    private $treeValueDict;
    /** recursive pointer */
    private $batchId;
    /** path to the root json file 0.json */
    private $cacheRootJsonFilePath;
    /** path to the json file passed to the api */
    private $jsonDataFilePath;

    // property lookup source
    protected $lookupSource = 'tree';
    // set to true when data is loaded into the object, used by cache mechanism
    protected $dataIsLoaded;

    /**
     * Create the JSON tree handler.
     */
    public function __construct($config) {
        $this->config       = $config;
        $this->jsonTempDir  = $config->getOptimizerTempDir().__CLASS__.'/';
        $this->cacheRootJsonFilePath = $this->jsonTempDir.'0.json';
    }
    
    /**
     * Set new config settings via a new Config object.
     * 
     * @param type $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }

    /**
     * Load the JSON tree file into the handler.
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     * @throws Mobi_Mtld_DA_Exception_JsonException
     */
    public function loadTreeFromFile($jsonDataFilePath) {
        $this->jsonDataFilePath = $jsonDataFilePath;

        if (!$this->config->getCacheProvider()) {
            $this->__loadTreeFromFile();
        }
    }

    private function __loadTreeFromFile() {
        $jsonDataFilePath   = $this->jsonDataFilePath;
        $this->dataIsLoaded = true;

        if ($this->config->getUseTreeOptimizer()) {

            if ($this->config->getIgnoreDataFileChanges()) {
                // no checkings, assume the cached root json file exists
                return $this->loadCachedTreeRoot();

            } else {
                // if json root cached file exists and it is not older than the passed json file
                if (file_exists($this->cacheRootJsonFilePath)) {
                    $fileKey = @file_get_contents($this->jsonTempDir.self::CACHED_JSON_INFO_FILE_NAME);
                    if (!file_exists($jsonDataFilePath) || $fileKey === self::getFileId($jsonDataFilePath)) {
                        return $this->loadCachedTreeRoot();
                    }
                }
            }
            // if new json or no json cache then populate json cache
            $this->__populateCache();
        } else {
            parent::loadTreeFromFile($jsonDataFilePath);
        }
    }

    /**
     * try to load the root cached json file, if not exists try to load and populate the original
     */
    private function loadCachedTreeRoot() {
        try {
            parent::loadTreeFromFile($this->cacheRootJsonFilePath);
            $this->jsonBatchDir = $this->jsonTempDir.$this->getDataCreationTimestamp().'/';
            $this->lookupValue  = false;
        } catch (Mobi_Mtld_DA_Exception_DataFileException $x) {
            $this->__populateCache();
        }
    }

    /**
     * Break down the DeviceAtlas JSON data file into smaller files and save the
     * batch into "/da-api-temp-dir/batch-cache-dir".
     *
     * @param  bool force=false false=do not populate if batch already exists
     * @return bool true=populated json batch cache files
     */
    public function populateCache($jsonDataFilePath, $force=false) {
        $this->jsonDataFilePath = $jsonDataFilePath;
        $this->__populateCache($force);
    }

    /**
     * for internal cache populations usages
     */
    private function __populateCache($force=true) {
        parent::loadTreeFromFile($this->jsonDataFilePath);

        $this->jsonBatchDir = $this->jsonTempDir.$this->getDataCreationTimestamp().'/';
        $this->lookupValue  = false;

        // if json is already cached (dir exists) only do it if forced
        if (file_exists($this->jsonBatchDir)) {
            if (!$force) {
                return false;
            }
        } else {
            if (!@mkdir($this->jsonBatchDir, 0755, true)) {
                self::throwFileWrite($this->jsonBatchDir);
            }
        }

        $this->batchId = 0;
        $this->cacheTree($this->tree['t']['c']);
        $this->fixValues($this->tree['uar']['rg']);
        if ($this->clientProps) {
            $this->fixValues($this->tree['cpr']['rg']);
        }

        unset($this->tree['v']);

        // write the batch root node
        if (!@file_put_contents($path=$this->jsonBatchDir.'0.json', json_encode($this->tree))) {
            self::throwFileWrite($path);
        }

        $this->cleanup();
        return true;
    }

    /**
     * Remove the old batches and create the new root json.
     */
    private function cleanup() {
        // latest cached file key
        if (!@file_put_contents($p=$this->jsonTempDir.self::CACHED_JSON_INFO_FILE_NAME, self::getFileId($this->jsonDataFilePath))) {
            self::throwFileWrite($p);
        }
        // temp index
        $indexPath    = $this->jsonTempDir.self::CACHED_JSON_HISTORY_FILE_NAME;
        $newBatchs[]  = $newBatchUtc = $this->getDataCreationTimestamp();
        $oldBatch     = file_exists($indexPath)? explode(';', file_get_contents($indexPath)): array();
        sort($oldBatch);
        // keep the newer batches
        for ($i = 1; $i < self::$MAX_CACHE_JSON_FILES; $i++) {
            $batch = array_pop($oldBatch);
            if ($batch) {
                $newBatchs[] = trim($batch);
            }
        }
        // remove the old batches
        foreach ($oldBatch as $batch) {
            if (trim($batch) !== trim($newBatchUtc)) {
                self::rmdir($this->jsonTempDir.trim($batch));
            }
        }
        // write the batch root node
        if (!@file_put_contents($indexPath, implode(";", $newBatchs))) {
            self::throwFileWrite($indexPath);
        }
        // copy the root tree on directoy up
        copy($this->jsonBatchDir.'0.json', $this->cacheRootJsonFilePath);
        touch($this->cacheRootJsonFilePath, filemtime($this->jsonDataFilePath));
    }

    /**
     * Get a unique identifier a json-file e.g. (filetime, filesize), to know when the file is changed
     */
    static private function getFileId($filePath) {
        return filemtime($filePath).filesize($filePath);
    }

    /**
     * Swap value ids with values in a tree node
     */
    private function fixValues(&$node) {
        foreach ($node as $key => &$value) {
            if ($key === 'v') {
                $value = $this->tree['v'][$value];
            } elseif (is_array($value)) {
                if ($key === 'p') {
                    foreach ($value as &$valueId) {
                        if (is_array($valueId)) {
                            $this->fixValues($valueId);
                        } else {
                            $valueId = $this->tree['v'][$valueId];
                        }
                    }
                } else {
                    $this->fixValues($value);
                }
            }
        }
    }

    /**
     * Break down tree and save to files.
     */
    private function cacheTree(&$tree, $depth=0) {
        $children = 0;
        foreach ($tree as $key => &$value) {
            if (isset($value['c'])){
                $children += $this->cacheTree($value['c'], $depth+1);
            }
            if (isset($value['d'])) {
                $children += count($value['d']);
                // replace value ids with values
                foreach ($value['d'] as &$valueId) {
                    $valueId = $this->tree['v'][$valueId];
                }
            }
        }

        if ($depth && $children >= self::$MIN_BATCH_SIZE) {
            $this->batchId++;
            if (!@file_put_contents($path=$this->jsonBatchDir.$this->batchId.'.json', json_encode($tree))) {
                self::throwFileWrite($path);
            }
            $tree     = $this->batchId;
            $children = 1;
        }
        
        return $children;
    }

    /**
     * Throw a file/directory write exception
     */
    static private function throwFileWrite($path) {
        require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/FileWriteException.php';
        throw new Mobi_Mtld_DA_Exception_FileWriteException(
            "Data tree optimizer could not create '".$path.
            "' lack of write permissions or a full disk can cause this problem."
        );
    }

    /**
     * Remove all tree optimization cache files
     */
    public function clearCache() {
        self::rmdir($this->jsonTempDir);
    }

    /**
     * Recursively remove files and dirs.
     */
    static private function rmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== '.' && $object !== '..') {
                    if (filetype($dir.'/'.$object) === 'dir') {
                        self::rmdir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                } 
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Get properties from tree walk/ua/client-side and put them in the tree.properties
     * use cached properties if exists.
     *
     * @param string userAgent       user-agent string (from the original User-Agent header) to be used for detecting ua-props
     * @param array  stockUserAgents list of candidate user-agent strings to be used for the tree walk
     * @param string clientside      client side properties or null
     */
    public function putProperties($userAgent, $stockUserAgents, $clientside) {
        // if cache provider
        $cacheProvider = $this->config->getCacheProvider();
        
        $this->lookupSource = $this->config->getUseTreeOptimizer()?
            Mobi_Mtld_DA_Device_DeviceApi::LOOKUP_SOURCE_OPTIMIZED_TREE:
            Mobi_Mtld_DA_Device_DeviceApi::LOOKUP_SOURCE_TREE;

        if ($cacheProvider) {
            // create cache key
            $userAgentKey = md5($userAgent.serialize($stockUserAgents));
            $cacheKey     = $clientside? $userAgentKey: $userAgentKey.md5($clientside);
            if ($this->config->getIncludeUaProps()) {
                $cacheKey .= 'u';
            }
            if ($this->config->getIncludeLangProps()) {
                $cacheKey .= 'l';
            }
            if ($this->config->getIncludeMatchInfo()) {
                $cacheKey .= 'm';
            }
            // get properties from cache if possible
            $cached = $cacheProvider->get($cacheKey);
            if ($cached) {
                $this->properties   = $cached;
                $this->lookupSource = Mobi_Mtld_DA_Device_DeviceApi::LOOKUP_SOURCE_CACHE;
            } else {
                // if no cache then get properties from api and cache the result
                if (!$this->dataIsLoaded) {
                    $this->__loadTreeFromFile();
                }
                parent::putProperties($userAgent, $stockUserAgents, $clientside);
                $cacheProvider->set($cacheKey, $this->properties);
            }
        } else {
            parent::putProperties($userAgent, $stockUserAgents, $clientside);
        }
    }

    /**
     * Get the source properties fetch source to be used for debugging.
     *
     * @return string 'tree' or 'optimized tree' or 'cache'
     */
    public function getLookupSource() {
        return $this->lookupSource;
    }

    /**
     * Get the list of all available property names from the tree (not contains client side props)
     */
    public function getPropertyNames() {
        if (!$this->dataIsLoaded) {
            $this->__loadTreeFromFile();
        }
        return $this->tree['p']; // [property-type-property-name,]
    }

    /**
     * Get data file version
     */
    public function getDataVersion() {
        if (!$this->dataIsLoaded) {
            $this->__loadTreeFromFile();
        }
        return $this->tree['$']['Ver'];
    }

    /**
     * Get data file creation timestamp
     */
    public function getDataCreationTimestamp() {
        if (!$this->dataIsLoaded) {
            $this->__loadTreeFromFile();
        }
        return $this->tree['$']['Utc'];
    }

    public function getDataRevision() {
        if (!$this->dataIsLoaded) {
            $this->__loadTreeFromFile();
        }
        return $this->tree['$']['Rev'];
    }
}
