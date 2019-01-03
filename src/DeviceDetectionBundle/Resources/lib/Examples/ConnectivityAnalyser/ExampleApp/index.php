<?php
/**
 * A practical example of how and why to use DeviceAtlas Connectivity Analyser
 *
 * In this example user's connection performance is tested then depending on the
 * result a different images is chosen.
 *
 * To simulate the medium and low connection quality remove comments (line 29-31)
 * For medium and low quality connections user will get a slightly lower
 * resolution smaller image which is smaller in size too.
 */


// Make sure to start the session before using!
session_start();

// Include
require_once dirname(__FILE__).'/../../../ExtraTools/ConnectivityAnalyser/ConnectivityAnalyser.php';

// Analyse connectivity for current user
$connectivityAnalyser = new Mobi_Mtld_DA_ConnectivityAnalyser();

// Get result
$duration = round($connectivityAnalyser->getDuration(), 1);
$quality  = $connectivityAnalyser->getQuality();

// To see how the page changes for different connection qualities remove the
// comment for that quality (override result)
//$quality = Mobi_Mtld_DA_ConnectivityAnalyser::LOW_QUALITY;
//$quality = Mobi_Mtld_DA_ConnectivityAnalyser::MEDIUM_QUALITY;
//$quality = Mobi_Mtld_DA_ConnectivityAnalyser::HIGH_QUALITY;

// The goal is to display a beautiful photo.
// The appropriate sized and compressed photo is selected based on user's
// connection performance:
switch ($quality) {

    // Poor quality connection
    //    deserves to see the photo, but a smaller and lower quality
    case Mobi_Mtld_DA_ConnectivityAnalyser::LOW_QUALITY:
        $photo = array(
            'images/dachstein-low-quality.jpg',
            '38.4',
            '300x200',
            '35'
        );
        break;

    // Medium quality connection
    //    not the highest quality but a good quality medium sized photo
    case Mobi_Mtld_DA_ConnectivityAnalyser::MEDIUM_QUALITY:
        $photo = array(
            'images/dachstein-medium-quality.jpg',
            '107.3',
            '300x333',
            '95'
        );
        break;

    // High quality connection
    //    the more detailed larg and high quality photo
    // Mobi_Mtld_DA_ConnectivityAnalyser::HIGH_QUALITY
    default:
        $photo = array(
            'images/dachstein-high-quality.jpg',
            '337.5',
            '600x399',
            '100'
        );
}
?>

<!DOCTYPE html>
<html>

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link type="text/css" rel="stylesheet" href="css/style.css" media="all"/>
    <title>DeviceAtlas Connectivity Analyser</title>
  </head>

  <body>
    <p>
      <img src="images/deviceatlas.png" alt="Device Atlas"/>
    </p>
    <h1>Result</h1>

    <div id="results">
        <div id="loadtime">
        Load Time:
        <span><?php echo $duration; ?>ms</span>
      </div>

        <div id="experience">
        End user experience:
        <span class="<?php echo $quality; ?>">
          <?php echo $quality; ?>
        </span>
      </div>
    </div>

    <div id="photo-container">
      <img src="<?php echo $photo[0]; ?>" alt="dachstein"/>
      <p><?php echo $photo[0]; ?></p>
      <p><?php echo $photo[1]; ?>kB</p>
      <p><?php echo $photo[2]; ?> (pixels)</p>
      <p>jpeg quality: <?php echo $photo[3]; ?></p>
    </div>
  </body>

</html>
