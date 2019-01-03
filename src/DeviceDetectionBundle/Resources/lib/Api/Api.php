<?php
/*
 * package Mobi\Mtld\DA
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA (deprecated);
require_once dirname(__FILE__).'/Device/DeviceApi.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/JsonException.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/InvalidPropertyException.php';

/**
 * Used to load the recognition tree and perform lookups of all properties, or
 * individual properties.
 * 
 * <b>Note:</b> Due to limitations in the level of recursion allowed, versions of PHP
 * older than 5.2.3 will be unable to load the JSON data file.
 * i.e. DeviceAtlas must be run with PHP version 5.2.3 or later.
 * 
 * Typical usage is as follows:
 * 
 * <pre>
 * $tree = Mobi_Mtld_DA_Api::getTreeFromFile("json/sample.json");
 * $props = Mobi_Mtld_DA_Api::getProperties($tree, "Nokia6680...");
 * </pre>
 * 
 * Note that you should normally use the user-agent that was received in
 * the device's HTTP request. In a PHP environment, you would do this as follows:
 * 
 * <pre>
 * $ua = $_SERVER['HTTP_USER_AGENT'];
 * $displayWidth = Mobi_Mtld_DA_Api::getPropertyAsInteger($tree, $ua, "displayWidth");
 * </pre>
 * 
 * (Also note the use of the strongly typed property accessor)
 * 
 * Third-party Browsers
 * 
 * In some contexts, the user-agent you want to recognise may have been provided in a
 * different header. Opera's mobile browser, for example, makes requests via an
 * HTTP proxy, which rewrites the headers. in that case, the original device's
 * user-agent is in the HTTP_X_OPERAMINI_PHONE_UA header, and the following code
 * could be used:
 * 
 * <pre>
 * $opera_header = "HTTP_X_OPERAMINI_PHONE_UA";
 * if (array_key_exists($opera_header, $_SERVER) {
 *   $ua = $_SERVER[$opera_header];
 * } else {
 *   $ua = $_SERVER['HTTP_USER_AGENT'];
 * }
 * $displayWidth = Mobi_Mtld_DA_Api::getPropertyAsInteger($tree, $ua, "displayWidth");
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
 * $props = Mobi_Mtld_DA_Api::getProperties($tree, $ua, $cookie_contents);
 * </pre>
 * @package Mobi\Mtld\DA
 * @version 2.0
 * @author Afilias Technologies Ltd
 * @deprecated This API is deprecated, it is highly recommended to use the new {@link Mobi_Mtld_DA_Device_DeviceApi DeviceApi} instead.
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Api {
    /** Min PHP version required for this API. */
	const MIN_PHP_VERSION = '5.2.3';

	/**
	 * Returns a loaded JSON tree from a string of JSON data.
	 *
	 * Some properties cannot be known before runtime and can change from user-agent to
	 * user-agent. The most common of these are the OS Version and the Browser Version. This
	 * API is able to dynamically detect these changing properties but introduces a small
	 * overhead to do so. To disable returning these extra properties set
	 * <i>includeChangeableUserAgentProperties</i> to <b>false</b>.
	 * 
	 * @param string &$json The string of json data.
	 * @param boolean $includeChangeableUserAgentProperties Also detect changeable user-agent properties
	 * @return array The loaded JSON tree
	 * 
	 * @throws Mobi_Mtld_DA_Exception_JsonException
	 */
	public static function getTreeFromString(&$json, $includeChangeableUserAgentProperties=true) {
        $config = new Mobi_Mtld_DA_Device_Config();
        $config->setIncludeUaProps($includeChangeableUserAgentProperties);

        $deviceApi = new Mobi_Mtld_DA_Device_DeviceApi($config);

        try {
            $deviceApi->loadDataFromString($json);
        } catch (Mobi_Mtld_DA_Exception_JsonException $ex) {
            throw new Mobi_Mtld_DA_Exception_JsonException($ex->getMessage(), $ex->getCode());
        } catch (Mobi_Mtld_DA_Exception_Exception $ex) {
            throw new Mobi_Mtld_DA_Exception_JsonException("Could not load JSON data", Mobi_Mtld_DA_Exception_JsonException::BAD_DATA);
        }

        return array('api' => $deviceApi);
    }

	/**
	 * Returns a tree from a JSON file.
	 * 
	 * Use an absolute path name to be sure of success if the current working directory is not clear.
	 *
	 * Some properties cannot be known before runtime and can change from user-agent to
	 * user-agent. The most common of these are the OS Version and the Browser Version. This
	 * API is able to dynamically detect these changing properties but introduces a small
	 * overhead to do so. To disable returning these extra properties set
	 * <i>includeChangeableUserAgentProperties</i> to <b>false</b>.
	 * 
	 * @param string $filename The location of the file to read in.
	 * @param boolean $includeChangeableUserAgentProperties
	 * @return array &$tree Previously generated tree
	 * 
	 * @throws Mobi_Mtld_DA_Exception_JsonException
	 */
	public static function getTreeFromFile($filename, $includeChangeableUserAgentProperties=true) {
        $config = new Mobi_Mtld_DA_Device_Config();
        $config->setIncludeUaProps($includeChangeableUserAgentProperties);

        $deviceApi = new Mobi_Mtld_DA_Device_DeviceApi($config);

        try {
            $deviceApi->loadDataFromFile($filename);
        } catch (Mobi_Mtld_DA_Exception_JsonException $ex) {
            throw new Mobi_Mtld_DA_Exception_JsonException($ex->getMessage(), $ex->getCode());
        } catch (IOException $ex) {
            throw new IOException($ex);
        } catch (Exception $ex) {
            throw new Mobi_Mtld_DA_Exception_JsonException("Could not load JSON data", Mobi_Mtld_DA_Exception_JsonException::BAD_DATA);
        }

        return array('api' => $deviceApi);
    }

	/**
	 * Get the generation date for this tree.
	 * 
	 * @param array &$tree Previously generated tree
	 * @return string The time/date the tree was generated.
	 */
	public static function getTreeGeneration(array &$tree) {
        return gmdate('Y-m-d h:i:s', $tree['api']->getDataCreationTimestamp());
	}

	/**
	 * Get the generation date for this tree as a UNIX timestamp.
	 * 
	 * @param array &$tree Previously generated tree
	 * @return integer The time/date the tree was generated.
	 */
	public static function getTreeGenerationAsTimestamp(array &$tree) {
		return $tree['api']->getDataCreationTimestamp();
	}

	/**
	 * Returns the revision number of the tree
	 *
	 * @param array &$tree Previously generated tree
	 * @return integer revision
	 */
	public static function getTreeRevision(array &$tree) {
		return $tree['api']->getDataRevision();
	}

	/**
	 * Returns the revision number of this API
	 * 
	 * @return integer revision
	 */
	public static function getApiRevision() {
		return 28284;
	}

	/**
	 * Returns all properties available for all user agents in this tree,
	 * with their data type names.
	 *
	 * @param array &$tree Previously generated tree
	 * @return array properties
	 */
	public static function listProperties(array &$tree) {
        $types = array();

        foreach ($tree['api']->getPropertyNames() as $propertyName) {
           $types[$propertyName->getName()] = strtolower($propertyName->getDataType());
        }

        return $types;
	}

	/**
	 * Returns an array of known properties (as strings) for the user agent
	 * 
	 *		= or =
	 * 
	 * Returns an array of known properties merged with properties from the client
	 * side JavaScript. The client side JavaScript sets a cookie with collected
	 * properties. The contents of this cookie must be passed to this method for it
	 * to work. The client properties over-ride any properties discovered from the
	 * main JSON data file.
	 *
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return array properties Property name => Property value
	 * 
	 * @throws Mobi_Mtld_DA_Exception_JsonException
	 */
	public static function getProperties(array &$tree, $userAgent, $cookie = null) {
        $propsOut = array();
        foreach ($tree['api']->getProperties($userAgent, $cookie) as $name => $prop) {
            $propsOut[$name] = $prop->value();
        }
        return $propsOut;
	}

	/**
	 * Returns an array of known properties (as typed) for the user agent
	 * 
	 *		= or =
	 * 
	 * Returns an array of known properties merged with properties from the client
	 * side JavaScript. The client side JavaScript sets a cookie with collected
	 * properties. The contents of this cookie must be passed to this method for it
	 * to work. The client properties over-ride any properties discovered from the
	 * main JSON data file.
	 *
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return array properties. Property name => Typed property value
	 * 
	 * @throws Mobi_Mtld_DA_Exception_JsonException
	 */
	public static function getPropertiesAsTyped(array &$tree, $userAgent, $cookie = null) {
        $propsOut = array();
        foreach ($tree['api']->getProperties($userAgent, $cookie) as $name => $prop) {
            switch ($prop->getDataTypeId()) {
                case Mobi_Mtld_DA_DataType::BOOLEAN:
                    $propsOut[$name] = is_string($prop->value())?
                        ($prop->value() === '1' || strtolower($prop->value()) === 'true'):
                        (bool)$prop->value();
                    break;
                case Mobi_Mtld_DA_DataType::INTEGER:
                    $propsOut[$name] = (int)$prop->value();
                    break;
                default:
                    $propsOut[$name] = $prop->asString();
            }
        }
        return $propsOut;
	}

	/**
	 * Returns a value for the named property for this user agent
	 *
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string $property The name of the property to return
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return string property
	 * 
	 * @throws Mobi_Mtld_DA_Exception_UnknownPropertyException
	 * @throws Mobi_Mtld_DA_Exception_InvalidPropertyException
	 * @throws Mobi_Mtld_DA_Exception_JsonException 
	 */
	public static function getProperty(array &$tree, $userAgent, $property, $cookie = null) {
        $propertyObj = $tree['api']->getProperties($userAgent, $cookie)->get($property);

        if (!$propertyObj) {
            $names = $tree['api']->getPropertyNames();
            if (isset($names[$property])) {
                throw new Mobi_Mtld_DA_Exception_InvalidPropertyException("The property \"" . $property . "\" does not exist for the User-Agent:\"" . userAgent . "\"");
            }
            throw new Mobi_Mtld_DA_Exception_UnknownPropertyException("The property \"" . $property . "\" is not known in this tree.");
        }

        return $propertyObj->asString();
	}

	/**
	 * Strongly typed property accessor.
	 * 
	 * Returns a string property.
	 * (Throws an exception if the property is actually of another type.)
	 * 
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string $property The name of the property to return
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return string property
	 *  
	 * @throws Mobi_Mtld_DA_Exception_UnknownPropertyException
	 * @throws Mobi_Mtld_DA_Exception_InvalidPropertyException
	 * @throws Mobi_Mtld_DA_Exception_JsonException 
	 *
	 */
	public static function getPropertyAsString(array &$tree, $userAgent, $property, $cookie = null) {
		return self::getProperty($tree, $userAgent, $property, $cookie);
	}
	
	/**
	 * Strongly typed property accessor.
	 * 
	 * Returns a boolean property.
	 * (Throws an exception if the property is actually of another type.)
	 * 
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string $property The name of the property to return
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return boolean property
	 * 
	 * @throws Mobi_Mtld_DA_Exception_UnknownPropertyException
	 * @throws Mobi_Mtld_DA_Exception_InvalidPropertyException
	 * @throws Mobi_Mtld_DA_Exception_JsonException 
	 */
	public static function getPropertyAsBoolean(array &$tree, $userAgent, $property, $cookie = null) {
        $val = self::getProperty($tree, $userAgent, $property, $cookie);
        return is_string($val)?
            ($val === '1' || strtolower($val) === 'true'): (bool)$val;
	}

	/**
	 * Strongly typed property accessor.
	 * 
	 * Returns a date property.
	 * (Throws an exception if the property is actually of another type.)
	 *
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string $property The name of the property to return
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return string property
	 *  
	 * @throws Mobi_Mtld_DA_Exception_UnknownPropertyException
	 * @throws Mobi_Mtld_DA_Exception_InvalidPropertyException
	 * @throws Mobi_Mtld_DA_Exception_JsonException 
	 *
	 */
	public static function getPropertyAsDate(array &$tree, $userAgent, $property, $cookie = null) {
		return getProperty($tree, $userAgent, $property, $cookie);
	}

	/**
	 * Strongly typed property accessor.
	 * 
	 * Returns an integer property.
	 * (Throws an exception if the property is actually of another type.)
	 *
	 * @param array &$tree Previously generated tree
	 * @param string $userAgent String from the device's User-Agent header
	 * @param string $property The name of the property to return
	 * @param string|null $cookie The contents of the cookie containing the client side properties
	 * @return integer property
	 * 
	 * @throws Mobi_Mtld_DA_Exception_UnknownPropertyException
	 * @throws Mobi_Mtld_DA_Exception_InvalidPropertyException
	 * @throws Mobi_Mtld_DA_Exception_JsonException 
	 *
	 */
	public static function getPropertyAsInteger(array &$tree, $userAgent, $property, $cookie = null) {
		return (int)self::getProperty($tree, $userAgent, $property, $cookie);
	}

    /**
     * Convert interface data types to tree property types
     */
    static private function getPropertyTypeAsString($type) {
        switch ($type) {
            case Mobi_Mtld_DA_DataType::STRING:
                return 's';
            case Mobi_Mtld_DA_DataType::BOOLEAN:
                return 'b';
            case Mobi_Mtld_DA_DataType::INTEGER:
                return 'i';
            case Mobi_Mtld_DA_DataType::DOUBLE:
                return 'd';
        }
        return 'unknown';
    }
}
