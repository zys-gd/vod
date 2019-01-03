<?php
/**
 * Content Adaptation Example using the DeviceAtlas DeviceApiWeb
 *
 * This sample code uses the DeviceAtlas DeviceApiWeb object to get properties
 * for the device making the current request then uses some basic property values
 * to choose a suitable template to wrap around the contents.
 *
 * Please try this example on a PC and various devices such as mobile phones,
 * tablets, etc. to see how the API works.
 *
 * The Plan: First, let's plan what we want to do and what this example is about.
 * Let's say we have different templates/css/etc. and each one is created for a
 * group of device types. When users visit our website, they will experience a
 * user-interface specially designed for their device.
 * The web-site pages will provide their contents and then they use DeviceAtlas
 * to get the device properties and choose the best user-interface for the device
 * to wrap around the contents.
 * In this example this file gets the request, then used DeviceAtlas to get
 * properties, checks few properties and selects a template to display. There
 * are five templates for desktop, ereader, mobile, tablet and low-end device
 * experience located in the /templates directory. Some of this templates use
 * the properties to further fine-tune the user-interface.
 *
 * Note: Including the DeviceAtlas Client side component may be used in this
 * page to give more accurate results.
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
// UI template files
define('DESKTOP_UI_TEMPLATE', 'desktop.php');
define('TABLET_UI_TEMPLATE',  'tablet.php');
define('WML_UI_TEMPLATE',     'wml.php');
define('EREADER_UI_TEMPLATE', 'ereader.php');
define('MOBILE_UI_TEMPLATE',  'mobile.php');


/* (2) Create a DeviceApiWeb instance */
$deviceApi = new Mobi_Mtld_DA_Device_DeviceApiWeb();


// the default templates is set to desktop browsers
$template = DESKTOP_UI_TEMPLATE;

// bellow, we will detect device type and include the suitable UI template

// it is highly recommended to use the API in a try/catch block as several exceptions
// may be thrown, please see the API documentation to know about the exceptions
try {

    /* (3) Load the device atlas JSON data file */
    $deviceApi->loadDataFromFile(JSON_FILE);

    /* (4) Get properties for current request */
    $properties = $deviceApi->getProperties();

    /* (5) Gather data/content to be shown, in this example we skip this part */

    /* (6) Based on device properties choose the most suitable template/user-interface */

    // * it's a tablet device
    if ($properties->contains('isTablet', true)) {
        $template = TABLET_UI_TEMPLATE;
    }
    // * it's a mobile device
    elseif ($properties->contains('mobileDevice', true)) {
        // you can create conditions on various properties to distinct between
        // low-end and high-end devices

        // * low-end devices which only support WML but not basic XHTML which provides contents wrapped in WML
        if ($properties->contains('markup.wml1', true) && $properties->contains('markup.xhtmlBasic10', false)) {
            $template = WML_UI_TEMPLATE;
        }
        // * it's a E-Reader device
        elseif ($properties->contains('isEReader', true)) {
            $template = EREADER_UI_TEMPLATE;
        }
        // * it's a high-end mobile device
        else {
            $template = MOBILE_UI_TEMPLATE;
        }
    }
    // * it's a spam
    elseif ($properties->contains('isRobot', true)) {
        die('Hi bot, how was your day? mine was nice! I would like to show you the site-map here.');
    }

} catch (Mobi_Mtld_Da_Exception_DataFileException $ex) {
    die("Could not find Device Data file. Did you download a data file from https://deviceatlas.com/user to be referenced by this example?\n");

} catch (Exception $ex) {

    /* if errors have happened then choose the default template (desktop experience) */

}


//
// The property set will be available in the templates and can be very useful.
//
// * properties which describe device and browser features can be used to fine
//   tune the response.
// * properties which describe device and browser make, model and version can
//   be used to display or disable certain features based on the device
//   support level for them.
//
// include the suitable template
include "templates/$template";
