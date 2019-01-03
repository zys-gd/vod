<?php
/**
 * Redirection Example using the DeviceAtlas DeviceApiWeb
 *
 * This sample code uses the DeviceAtlas DeviceApiWeb object to get properties
 * for the current request then uses some basic property values to decide which
 * URL provides the most suitable content for the device making the request.
 *
 * Please try this example on a PC and various devices such as mobile phones,
 * tablets, etc. to see how the API works.
 *
 * Scenario: First, let's plan what we want to do and what this example is about.
 * Let's say we have different web sites and each one is created to give the best
 * content and user experience to a specific device type. Users will enter our
 * main web-site URL in their browsers, the source code which gets this first
 * request will use DeviceAtlas DeviceApi to detect the device type, then based
 * on the conclusions, the user will be redirected to the web-site which provides
 * the best experience for her/his device.
 * In this example four directories will mimic four different websites that give
 * specific service to "desktop browsers", "mobile device" and "tablet pcs". Each
 * directory contains a sample "index.html". This page mimics the landing page
 * which will detect the device type and redirects the user to one of the four
 * directories.
 *
 * Note: Including the DeviceAtlas Client side component to this page will give
 * more accurate results.
 *
 * Note: In this example the DeviceAtpiWeb will optimize the data file on the
 * first request. The cached optimized data files will be used by the next lookups.
 * The default config setting for using the data-file optimizer in DeviceApiWeb
 * is set to true.
 *
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */

// to see all errors only when in development environment
error_reporting(E_ALL);
ini_set('display_errors', 1);



/* (1) Include the DeviceApiWeb library */
require_once dirname(__FILE__).'/../../../Api/Device/DeviceApiWeb.php';

// Place the path to your JSON data here > > >
define('JSON_FILE', '/path/to/the/datafile.json');
// Our device oriented website URLs
define('DESKTOP_URL',        '/desktop');
define('TABLET_URL',         '/tablet');
define('HIGHEND_MOBILE_URL', '/mobile/highend');
define('LOWEND_MOBILE_URL',  '/mobile/lowend/index.wml');


/* (2) Create a DeviceApiWeb instance */
$deviceApi = new Mobi_Mtld_DA_Device_DeviceApiWeb();



// it is highly recommended to use the API in a try/catch block as several exceptions
// may be thrown, please see the API documentation to know about the exceptions
try {

    /* (3) Load the device atlas JSON data file */
    $deviceApi->loadDataFromFile(JSON_FILE);

    /* (4) Get properties for current request */
    $properties = $deviceApi->getProperties();

} catch (Mobi_Mtld_Da_Exception_DataFileException $ex) {
    die("Could not find Device Data file. Did you download a data file from https://deviceatlas.com/user to be referenced by this example?\n");

} catch (Exception $ex) {

    /* if errors have happened then redirect to the default URL (desktop experience) */
    redirectTo(DESKTOP_URL);
    exit;
}


/* (5) Based on device properties redirect to the proper URL */



// * It's a desktop browser
// * It's a device but masquerading itself as desktop browser to get desktop experience
if ($properties->contains('isBrowser', true) || $properties->contains('isMasqueradingAsDesktop', true)) {
    // redirect to the URL/website designed to handle desktop browser requests
    redirectTo(DESKTOP_URL);
}



// * It's a tablet device
if ($properties->contains('isTablet', true)) {
    // redirect to the URL/website designed to handle tablet requests
    redirectTo(TABLET_URL);
}



// * It's a mobile device
if ($properties->contains('mobileDevice', true)) {
    // you can create conditions on various properties to understand if the device
    // is low-end or high-end or even get more specific details on what it supports

    // redirect low-end devices which support WML but not basic XHTML to a URL/website
    // which provides contents wrapped in WML
    if ($properties->contains('markup.wml1', true) && $properties->contains('markup.xhtmlBasic10', false)) {
        redirectTo(LOWEND_MOBILE_URL);
    }

    // redirect high-end devices to the URL/website designed to handle their requests
    redirectTo(HIGHEND_MOBILE_URL);
}



// * It's a robot
if ($properties->contains('isRobot', true)) {
    die('Hi bot, how was your day? mine was nice! I would like to show you the site-map here.');
}



// * Anything not handled would be redirected to the default URL (desktop experience)
redirectTo(DESKTOP_URL);



/**
 * Redirect to URL
 */
function redirectTo($path) {
    header('Location: ' .getBaseUrl() . $path);
    exit;
}

/**
 * Get the BASE URL
 * Note that this is not a sophisticated solution but sufficient for this example
 */
function getBaseUrl() {
    return rtrim(str_replace('index.php', '', $_SERVER['REQUEST_URI']), '/');
}
