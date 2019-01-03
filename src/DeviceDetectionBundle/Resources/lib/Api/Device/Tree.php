<?php
/*
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;
require_once Mobi_Mtld_DA_DEVICE_API_PATH.'ClientProps.php';
require_once Mobi_Mtld_DA_DEVICE_API_PATH.'UaProps.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Properties.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/DataFileException.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/JsonException.php';

/**
 * This class is used by the main API class and should not be used directly.
 *
 * @package Mobi\Mtld\DA\Device
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Device_Tree {

    const API_ID                       = '1';
    protected static $MIN_JSON_VERSION = 0.7;

    // A list of http-headers which may contain the original user-agent.
    // if the tree does not contain KEY_UA_HEADERS then this list will be used
    public $stockUaHeaders = array(
        'x-device-user-agent',
        'x-original-user-agent',
        'x-operamini-phone-ua',
        'x-skyfire-phone',
        'x-bolt-phone-ua',
        'device-stock-ua',
        'x-ucbrowser-ua',
        'x-ucbrowser-device-ua',
        'x-ucbrowser-device',
        'x-puffin-ua',
    );

    // exception messages
    protected static $XMSG_JSON_LOAD                       = 'Unable to load Json data.';
    protected static $XMSG_JSON_VERSION                    = 'This version of the API requires a newer version of the JSON data.';
    protected static $XMSG_JSON_INVALID                    = 'Bad data loaded into the tree.';
    protected static $XMSG_CLIENT_PROPERTIES_NOT_SUPPORTED = 'JSON data does not support client properties.';
    protected static $XMSG_FILE_NOT_FOUND                  = 'File "%s" not found.';

    // the object containing API configs
    public $config         = null;
    // the JSON tree structure
    public $tree           = null;
    // object to collect ua props form
    protected $uaProps     = null;
    // object to parse, validate and collect clientside props form
    protected $clientProps = null;
    // the device id property index(id) - the tree walk may needs this
    protected $deviceIdPropNameId;
    // collects the property, any method or package member gets/manipulates/puts properties here
    public $properties;
    // true=node values are value id's and must be translated to the value
    public $lookupValue    = true;

    /**
     * Create the JSON tree handler.
     */
    public function __construct($config) {
        $this->config = $config;
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
        $json = @file_get_contents($jsonDataFilePath);
		if (!$json) {
            throw new Mobi_Mtld_DA_Exception_DataFileException(
                sprintf(self::$XMSG_FILE_NOT_FOUND, $jsonDataFilePath)
            );
		}
        $this->loadTreeFromString($json);
    }

    /**
     * Load the JSON tree into the handler.
     * @throws Mobi_Mtld_DA_Exception_JsonException
     */
    public function loadTreeFromString($json) {
        // for multiple data file loading in one session usages:
        // gc_collect_cycles();

        $this->tree = json_decode($json, true);

        /* validate data */
        if (!$this->tree) {
            throw new Mobi_Mtld_DA_Exception_JsonException(
                self::$XMSG_JSON_LOAD, Mobi_Mtld_DA_Exception_JsonException::JSON_DECODE
            );
        }
        if (!isset($this->tree['$'])) {
            throw new Mobi_Mtld_DA_Exception_JsonException(
                self::$XMSG_JSON_INVALID, Mobi_Mtld_DA_Exception_JsonException::BAD_DATA
            );
        }

        if (version_compare($this->tree['$']['Ver'], self::$MIN_JSON_VERSION) !== 1) {
            throw new Mobi_Mtld_DA_Exception_JsonException(
                self::$XMSG_JSON_VERSION, Mobi_Mtld_DA_Exception_JsonException::BAD_VERSION
            );
        }

        /* regex rules */
        if (!isset($this->tree['r'])) {
            $this->tree['r'] = array();
        }

        /* Prepare the user-agent rules branch */
        if (isset($this->tree[Mobi_Mtld_DA_Device_UaProps::UA_RULES]) && $this->config->getIncludeUaProps()) {
            $this->uaProps = new Mobi_Mtld_DA_Device_UaProps($this);
        }

        /* prepare client side properties */
        if (isset($this->tree['cpr'])) {
            $this->clientProps = new Mobi_Mtld_DA_Device_ClientProps($this);
        } else {
            $this->clientProps = null;
        }

        /* cache values from the tree which are used by the API */
        $this->deviceIdPropNameId = array_search('iid', $this->tree['p']);

        /* update stock user-agent headers form tree */
        if (isset($this->tree['h']['sl'])) {
            $this->stockUaHeaders = $this->tree['h']['sl'];
        }
    }

    /**
     * Get the list of all available property names from the tree (not contains client side props)
     */
    public function getPropertyNames() {
        return $this->tree['p']; // [property-type-property-name,]
    }

    /**
     * Get data file version
     */
    public function getDataVersion() {
        return $this->tree['$']['Ver'];
    }

    /**
     * Get data file creation timestamp
     */
    public function getDataCreationTimestamp() {
        return $this->tree['$']['Utc'];
    }

    public function getDataRevision() {
        return $this->tree['$']['Rev'];
    }

    /**
     * Get properties from tree walk/ua/client-side and put them in the tree.properties
     *
     * @param userAgent        user-agent string (from the original User-Agent header) to be used for detecting ua-props
     * @param stockUserAgents  list of candidate user-agent strings to be used for tree walk
     * @param clientside       may be null
     */
    public function putProperties($userAgent, $stockUserAgents, $clientside) {
        // to reset (clear) properties if full from a previous call
        $this->properties = new Mobi_Mtld_DA_Properties();
        $this->putTreeWalkProperties($userAgent, $stockUserAgents);

        if ($clientside) {
            if (!$this->clientProps) {
                // stop if the JSON file does not contain the required CPR section
                require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/ClientPropertiesException.php';
                throw new Mobi_Mtld_DA_EXCEPTION_ClientPropertiesException(self::$XMSG_CLIENT_PROPERTIES_NOT_SUPPORTED);
            }
            $this->clientProps->putProperties($clientside);
        }
    }

    /**
     * Get properties from tree walk/ua and put them in the tree.properties
     *
     *   if stockUserAgents != null
     *       - iterate over stockUserAgents
     *         for each item: tree-walk and stop iteration if result has deviceId
     *       - use userAgent for detecting the ua-props
     *
     *   if stockUserAgents == null
     *       - use userAgent for tree walk
     *       - use userAgent for detecting the ua-props
     *
     * @param userAgent       user-agent string (from the original User-Agent header)
     * @param stockUserAgents list of candidate user-agent strings to be used for tree walk
     */
    public function putTreeWalkProperties($userAgent, $stockUserAgents=null) {
        // props2Vals = {property-id-from-tree-p: value-id-from-tree-v,}
        $props2Vals = array();
        $regexes    = $this->tree['r'][self::API_ID];
        $treeMain   = $this->tree['t'];
        $matched    = '';
        $userAgent  = trim($userAgent);

        if ($stockUserAgents) {
            foreach ($stockUserAgents as $stockUserAgent) {
                $matched = '';
                $this->seekProperties($treeMain, $stockUserAgent, $props2Vals, $matched, $regexes);
                if (isset($props2Vals[$this->deviceIdPropNameId])) {
                    break;
                }
            }
        } else {
            $this->seekProperties($treeMain, $userAgent, $props2Vals, $matched, $regexes);
        }

        // put the detected properties which are as {property-id-from-tree-p: value-id-from-tree-v,}
        // into the (Properties) properties object
        foreach ($props2Vals as $name => $value) {
            $name = $this->tree['p'][$name];
            $this->properties->put(
                substr($name, 1),
                new Mobi_Mtld_DA_Property(
                    $this->lookupValue? $this->tree['v'][$value]: $value,
                    $name[0]
                )
            );
        }

        // matched and un-matched ua parts
        if ($this->config->getIncludeMatchInfo()) {
            $this->properties->put('_matched',   new Mobi_Mtld_DA_Property($matched, 's'));
            $this->properties->put('_unmatched', new Mobi_Mtld_DA_Property(substr($userAgent, strlen($matched)), 's'));
        }

        // get ua-props from the original user-agent header
        if ($this->config->getIncludeUaProps() && $this->uaProps) {
            $this->uaProps->putProperties($userAgent, $props2Vals, null);
        }
    }

    /**
     * Seek properties for a user agent within a node. This is designed to be
     * recursed, and only externally called with the node representing the top
     * of the tree
     */
    private function seekProperties($node, $string, &$props2Vals, &$matched, $regexRules) {

        // reset tree walk
        if (isset($node['g'])) {
            $matched = '';
            $props2Vals = array();
            $this->seekProperties(
                    $this->tree['t'],
                    $this->tree['g'][$node['g']] . $string,
                    $props2Vals,
                    $matched,
                    $regexRules);
            return;
        }

        if (isset($node['d'])) {
            $props2Vals = $node['d'] + $props2Vals;
        }

        if (isset($node['c'])) {
            // rules - strip out parts of the UA
            if (isset($node['r'])) {
                foreach ($node['r'] as $ruleId) {
                    $string = preg_replace('/' . $regexRules[$ruleId] . '/', '', $string);
                }
            }

            // json lazy loading > > >
            if (is_numeric($node['c'])) {
                $content = @file_get_contents($this->jsonBatchDir.$node['c'].'.json');
                if (!$content) {
                    throw new Mobi_Mtld_DA_Exception_DataFileException(
                        sprintf(self::$XMSG_FILE_NOT_FOUND, $this->jsonBatchDir.$node['c'].'.json')
                    );
                }
                $content = json_decode($content, true);
                if (!$content) {
                    throw new Mobi_Mtld_DA_Exception_JsonException(
                        self::$XMSG_JSON_LOAD, Mobi_Mtld_DA_Exception_JsonException::JSON_DECODE
                    );
                }
                $node['c'] = $content;
            }
            // < < < json lazy loading

            for ($c = 1, $l = strlen($string) + 1; $c < $l; $c++) {
                $seek = substr($string, 0, $c);
                if (isset($node['c'][$seek])) {
                    $matched .= $seek;
                    $this->seekProperties(
                        $node['c'][$seek],
                        substr($string, $c),
                        $props2Vals,
                        $matched,
                        $regexRules
                    );
                    break;
                }
            }
        }
    }

    /**
     * Get the source properties fetch source to be used for debugging.
     *
     * @return string 'tree' or 'optimized tree' or 'cache'
     */
    public function getLookupSource() {
        return Mobi_Mtld_DA_Device_DeviceApi::LOOKUP_SOURCE_TREE;
    }
}
