<?php
/**
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

/**
 * DeviceAtlas CarrierApiWeb example app.
 */

require_once dirname(__FILE__).'/../../../Api/Carrier/CarrierApiWeb.php';

// CHANGE CARRIER DATA FILE PATH HERE > > >
$dataFilePath = '/path/to/the/carrier.dat';

$carrierApi   = null;
$props        = null;
$errorMsg     = null;

try {
    $carrierApi = new Mobi_Mtld_DA_Carrier_CarrierApiWeb();
    $carrierApi->loadDataFromFile($dataFilePath);

    // because of IP range changes in time, this IP may not resolve to a mobile network in the future
    // if so please try other IPs
    $ipv4 = '27.122.55.255';
    $props = $carrierApi->getProperties($ipv4);

} catch (IOException $ex) {
    // handle properly in production
    $errorMsg = $ex->getMessage();

} catch (Mobi_Mtld_DA_Exception_DataFileException $ex) {
    // handle properly in production
    $errorMsg = $ex->getMessage();
} catch (Exception $ex) {
    // other
    $errorMsg = $ex->getMessage();
}

?>

<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DeviceAtlas CarrierApi Example</title>
    <link type="text/css" rel="stylesheet" href="css/style.css" media="all" />
  </head>
  <body>
    <?php
    if ($errorMsg == null)
    {
        // output some informational data
        echo
          '<ul>' .
            "<li><strong>Data file path: </strong>$dataFilePath</li>" .
            "<li><strong>Data file date: </strong>" .
                date('M d Y H:i:s', strtotime($carrierApi->getDataFileCreationDate())) .
            '</li>' .
            "<li><strong>Data file copyright: </strong>" . $carrierApi->getDataFileCopyright() . '</li>' .
          '</ul>'
        ;

        // output the actual results from a lookup
        echo '<div id="results"><h2>Results</h2>';

        if (!$props) {
            echo "<p>Sorry! IP $ipv4 does not resolve to a mobile network. Please try another IP.</p>";

        } else {
            // print out the IP address used in the query
            echo
                // print out all the properties returned for this useragent
                '<p><strong>Properties:</strong>' .
                '<table>'
            ;

            foreach ($props as $key => $value) {
                echo '<tr><td class="propname">' . $key . ' = ' . $value . '</td></tr>';
            }

            echo '</table></div>';
        }

    } else {
        echo '<p class="error"><strong>Error: </strong>' . $errorMsg .'</p>';
    }
    ?>
    </body>
</html>
