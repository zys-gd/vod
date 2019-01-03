<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

require_once dirname(__FILE__).'/../../../Api/Carrier/CarrierApi.php';

/**
 * Example showing how to wrap the Carrier API in a Singleton so the same
 * instance can be used from different classes.
 *
 * NOTE: There may be better ways of sharing the CarrierApi instance such as
 * storing it in a ServletContext when run as part of a web application.
 *
 * @author Afilias Technologies Ltd
 */
class CarrierApiSingleton {

    private static $instance;
    private        $carrierApi;
    private static $_Lock;

    /**
     * A singleton needs a private constructor.
     */
    private function __construct() {
        $this->carrierApi = new Mobi_Mtld_DA_Carrier_CarrierApi();
    }

    /**
     * Get the instance of the CarrierApiSingleton or create it if it does not
     * yet exist.
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new CarrierApiSingleton();
        }
        return self::$instance;
    }

    /**
     * Wrap the file loading from the CarrierApi
     */
    public function loadFile($path) {
        $this->carrierApi->loadDataFromFile($path);
    }

    /**
     * Wrap the getProperties from the CarrierApi
     */
    public function getProperties($ipv4) {
        return $this->carrierApi->getProperties($ipv4);
    }

    /**
     * Necessary to prevent cloning this singleton...
     */
    public function __clone() {
        // that'll teach 'em - I'm a singleton!
        throw new CloneNotSupportedException();
    }
}

/**
 * CloneNotSupportedException
 * @author>dotMobi
 */
class CloneNotSupportedException extends Exception {

    const CLONE_NOT_SUPPORTED_MSG = 'Unknow/invalid host';

    public function __construct() {
        parent::__construct(self::CLONE_NOT_SUPPORTED_MSG);
    }
}

/**
 * Use the sinleton class
 */

$instance = CarrierApiSingleton::getInstance();

// CHANGE CARRIER DATA FILE PATH HERE > > >
$instance->loadFile('/path/to/the/carrier.dat');

print("\n\n first call \n\n");

// look up some properties
$props = $instance->getProperties('89.19.64.164');
foreach ($props as $key => $value) {
    print("\t" . $key . ' = ' . $value . "\n");
}

//....
//....
//....


print("\n\n second call \n\n");

// in some other class in your code you might need the CarrierApi again...
// you can call getInstance of the Singleton to get a reference to an
// already loaded API.
$instance2 = CarrierApiSingleton::getInstance();
// look up some more properties
$props2 = $instance2->getProperties('74.13.226.25');
foreach ($props2 as $key => $value) {
    print("\t" . $key . ' = ' . $value . "\n");
}

print("\n\n");
