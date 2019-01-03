<?php
/**
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

require_once dirname(__FILE__).'/../../../Api/Carrier/CarrierApi.php';

/**
 * Basic usage of the CarrierApi. To run this from the command line please to the
 * following:
 *
 * <pre>
 * % php BasicUsage.php
 * </pre>
 *
 * @author Afilias Technologies Ltd
 */
class BasicUsage {

    // CHANGE CARRIER DATA FILE PATH HERE > > >
    const DATA_FILE = '/path/to/the/carrier.dat';

    private $carrierApi;

    private function createCarrierApiObject() {
        print("\n=== Examples to show usage of the DeviceAtlas Carrier API. ===\n");

        $this->carrierApi = new Mobi_Mtld_DA_Carrier_CarrierApi();

        try {
            $this->carrierApi->loadDataFromFile(self::DATA_FILE);
        } catch (IOException $ex) {
            print($ex->getMessage());
            // handle properly in production
        } catch (Mobi_Mtld_DA_Exception_DataFileException $ex) {
            print($ex->getMessage());
            // handle properly in production
        }
    }

    /**
     * Example to print out all the possible property names and their data types.
     */
    private function example1() {
        print("\nExample 1: print out all possible property names\n");

        $propertyNames = $this->carrierApi->getPropertyNames();

        foreach ($propertyNames as $propertyName) {
            print(
                "\tname: \"" . $propertyName->getName() .
                "\" (\"" . $propertyName->getDataType() . "\")\n"
            );
        }
    }

    /**
     * Example to get all the properties for a given ipv4 address.
     * @param ipv4
     */
    private function example2($ipv4) {
        print("\nExample 2: get all properties for IP = $ipv4 \n");

        // get all properties
        $props = $this->carrierApi->getProperties($ipv4);

        if (!$props) {
	    print("\tSorry! IP $ipv4 does not resolve to a mobile network. Please try another IP.\n");
            return;
        }

        foreach ($props as $key => $value) {
            print("\t" . $key . ' = ' . $value . "\n");
        }

        print("\nExample 2a: get a specific property from Properties result\n");
        // .... use the properties ....
        if ($props->containsKey('networkOperator')) {
            $operatorName = $props->get('networkOperator')->asString();
            print("\tnetworkOperator: " . $operatorName . "\n");
        }
    }

    /**
     * Example to get a specific property for a given ipv4 address.
     * @param ipv4
     */
    private function example3($ipv4) {
        print("\nExample 3: get a specific property for IP = $ipv4 \n");

        // get a single property
        $mccProp = $this->carrierApi->getProperty($ipv4, 'mcc');

        if ($mccProp !== null) {
            $mcc = $mccProp->asString();
            print("\tMCC: " . $mcc . "\n");
        } else {
            print("\tMCC: no value\n");
        }
    }

    /**
     * @param args the command line arguments
     */
    public function __construct() {

	// because of IP range changes in time, this IP may not resolve to a mobile network in the future
        // if so please try other IPs
        $ip = '27.122.55.255';

        $this->createCarrierApiObject();
        $this->example1();
        $this->example2($ip);
        $this->example3($ip);
    }
}

new BasicUsage();
