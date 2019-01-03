#!/usr/bin/php
<?php
/**
 * DeviceAtlas DeviceApi CLI Basic Usage Example.
 * Detecting Apple iPhone models by providing client-side properties.
 *
 * This example demonstrates using the DeviceApi, for the sake of simplicity it
 * is made as a command line application.
 * Run this example from the command line as "php cli-iphone-models.php"
 *
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */

// to see all errors only when in development environment
error_reporting(E_ALL);
ini_set('display_errors', 1);



/* (1) Include the DeviceApi library */
require_once dirname(__FILE__).'/../../../../Api/Device/DeviceApi.php';

// Place the path to your JSON data here > > >
define('JSON_FILE', '/path/to/the/datafile.json');


/* Inputs for the API */

// a set of HTTP headers for the detection and getting the properties
$headers = array(
    'User-Agent'      => 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; en-gb) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.1',
    'Accept-Language' => 'en-US',
);

// client-side properties, if available the results will be more accurate
// this value is created by the DeviceAtlas client side component (see README.ClientSide.html)
$clientSide = 'bjs.webGl:1|bjs.geoLocation:1|bjs.webSqlDatabase:1|bjs.indexedDB:0|bjs.webSockets:1|bjs.localStorage:1|bjs.sessionStorage:1|bjs.webWorkers:1|bjs.applicationCache:1|bjs.supportBasicJavaScript:1|bjs.modifyDom:1|bjs.modifyCss:1|bjs.supportEvents:1|bjs.supportEventListener:1|bjs.xhr:1|bjs.supportConsoleLog:1|bjs.json:1|bjs.deviceOrientation:1|bjs.deviceMotion:1|bjs.touchEvents:1|bjs.querySelector:1|bhtml.canvas:1|bhtml.video:1|bhtml.audio:1|bhtml.svg:1|bhtml.inlinesvg:1|bcss.animations:1|bcss.columns:1|bcss.transforms:1|bcss.transitions:1|idisplayColorDepth:32|bcookieSupport:1|iorientation:90|sdevicePixelRatio:1|sdeviceAspectRatio:3/4|bflashCapable:0|baccessDom:1|buserMedia:0';



/* (2) Create a DeviceApi instance */
$deviceApi = new Mobi_Mtld_DA_Device_DeviceApi();



// it is highly recommended to use the API in a try/catch block as several exceptions
// may be thrown, please see the API documentation to know about the exceptions
try {

    /* (3) Load the device atlas JSON data file */
    $deviceApi->loadDataFromFile(JSON_FILE);

    /* (4) now we can use the DeviceApi instance to get properties

       - we can pass a set of HTTP headers or a user-agent string to the API
       - if we have the client-side properties supplied by the DeviceAtlas client
         side component then we can also pass it to the API to get more accurate results

       the function bellow will pass a set of HTTP headers and a client-side properties
       string to the DeviceApi instance and gets the properties, using and displaying
       them in various ways
     */
    basicUsage($deviceApi, $headers, $clientSide);

} catch (Mobi_Mtld_Da_Exception_DataFileException $ex) {
    print "Could not find Device Data file. Did you download a data file from https://deviceatlas.com/user to be referenced by this example?\n";

} catch (Exception $ex) {
    // all errors must be taken care of
    print "\nErrors:\n";
    print $ex->getMessage()."\n";
}



/**
 * Displaying properties in various ways
 * demonstrates the usage of "getProperties()" method and how to use it's output
 */
function basicUsage($deviceApi, $headers, $clientSide) {

    /* Get the properties, as you can see bellow if we have the client-side properties
       we can pass it as the second argument.

       the returned value is an instance of "Properties", this object
       contains the property set and is iterable

       Properties is an extension of an array of Property objects, think of a
       Properties instance as an array where the keys are property-names and
       the values are Property objects

       a Property instance wraps around a property value, providing different
       methods to get the value as a string, boolean or an int value

     */
    $properties = $deviceApi->getProperties($headers, $clientSide);

    // we iterate over the properties to display property names, data types and value
    print "-------------------------------------------------------------\n";
    print "All Properties:\n";

    foreach ($properties as $name => $property) {
        print
            $name.
            ' ('. $property->getDataType() .") :\n".
            "\t". $property->value(). "\n";
    }





    // iterating over the properties may not be a good example of real life usage
    // so here we demonstrate usages that are more likely to happen after a detection
    print "-------------------------------------------------------------\n";
    print "Using the Properties and Property objects:\n";

    // use Properties.contains() to check if a property has a specific value
    // if the property value is the same as expected, true will be returned
    // if the property value does not match, property does not exist or is invalid, false will be returned

    // check if mobileDevice is true?
    $isMobileDevice = $properties->contains('mobileDevice', true);

    // check is vendor is Samsung (case-sensitive)
    $isSamsung = $properties->contains('vendor', 'Samsung');

    // lets display something based on what we got
    print $isMobileDevice?
        "\n*** it's a mobile device ***\n":
        "\n*** it's not a mobile device ***\n";

    print $isSamsung?
        "\n*** Vendor is Samsung ***\n":
        "\n*** Vendor is not Samsung ***\n";





    // get a specific property value
    // before getting a property always check if the property exists in the set

    // to get the property value without considering the data type strictly
    $browserName1 = $properties->containsKey('browserName')? $properties->get('browserName')->value(): null;

    // you can get any property value as a string
    $browserName2 = $properties->containsKey('browserName')? $properties->get('browserName')->asString(): null;

    // you can use Properties.__get() - returns string or null if property not exists
    // this is the easiest way to get a property value when possible
    $browserName3 = $properties->browserName;

    // if you know the exact data type of a property you can get it as typed
    $yearReleased = $properties->containsKey('yearReleased')? $properties->get('yearReleased')->asInteger(): 0;

    // you can use Properties.__get() - returns boolean or null if property not exists
    $isBrowser    = $properties->isBrowser;

    // lets display the results
    print "\n*** 'browserName'  = $browserName1 ***\n";
    print "\n*** 'browserName'  = $browserName2 ***\n";
    print "\n*** 'browserName'  = $browserName3 ***\n";
    print "\n*** 'yearReleased' = $yearReleased ***\n";
    print "\n*** 'isBrowser'    = ".($isBrowser? 'true': 'false')." ***\n";

    // if you try to get the wrong data type from a property then an exception will be thrown
    try {
        if ($properties->containsKey('yearReleased')) {
            $yearReleased = $properties->get('yearReleased')->asBoolean();
        }
    } catch (Exception $x) {
        print "\n*** 'yearReleased' is not boolean ***\n";
    }





    // get the data type of a property
    if ($properties->containsKey('vendor')) {
        $dataType = $properties->get('vendor')->getDataType();
        print "\n*** The data type of 'vendor' is $dataType ***\n";
    }

    // check property data types
    if ($properties->containsKey('yearReleased')) {

        $dataTypeId = $properties->get('yearReleased')->getDataTypeId();
        switch ($dataTypeId) {
            case Mobi_Mtld_DA_DataType::INTEGER:
                print "\n*** 'yearReleased' is integer ***\n";
                break;
            case Mobi_Mtld_DA_DataType::BOOLEAN:
                print "\n*** 'yearReleased' is boolean ***\n";
                break;
            case Mobi_Mtld_DA_DataType::STRING:
                print "\n*** 'yearReleased' is string ***\n";
                break;
        }
    }
}
