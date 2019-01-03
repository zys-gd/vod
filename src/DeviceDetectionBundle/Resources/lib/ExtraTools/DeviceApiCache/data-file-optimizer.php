<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 *
 * This is a command line tool for the data file optimizer.
 * You can optimize a new DeviceAtlas Device Data JSON file which you downloaded
 * from the DeviceAtlas web-site using this tool, the API will use the optimized
 * data. Also you can remove all cache and temp files created by the optimizer.
 *
 */

//namespace Mobi_Mtld_DA_Device;
require_once dirname(__FILE__).'/../../Api/Device/DeviceApi.php';
require_once dirname(__FILE__).'/../../Api/Device/TreeOptimized.php';

/**
 * Optimize a DeviceAtlas JSON data file and populate the cache. The API will
 * use the latest optimized cached files.
 * Note that when using the API (configured to use optimized data file), when
 * a new data file is passed to the API "loadDataFromFile()" method if the file
 * is not optimized it will automatically be optimized. However this will cause
 * a small lag for the request which the optimization is done at. The bellow
 * function may be used instead.
 *
 * @param string jsonDataFilePath Path to the DeviceAtlas JSON file to be optimized
 * @param string tempDir=null If you manually set the temp directory to the API
 *        then pass it to this function
 * @param bool force=false false=do not populate if batch already exists
 */
function optimizeDataFile($jsonDataFilePath, $tempDir=null, $force=false) {
    $config = new Mobi_Mtld_DA_Device_Config();
    $config->setUseTreeOptimizer(true);
    if ($tempDir) {
        $config->setOptimizerTempDir($tempDir);
    }
    $tree = new Mobi_Mtld_DA_Device_TreeOptimized($config);
    $tree->populateCache($jsonDataFilePath, $force);
}

/**
 * Clear all files and directories which contain DeviceAtlas cached/temp files.
 *
 * @param string tempDir=null If you manually set the temp directory to the API
 *        then pass it to this function
 */
function clearOptimizationCache($tempDir=null) {
    $config = new Mobi_Mtld_DA_Device_Config();
    $config->setUseTreeOptimizer(true);
    if ($tempDir) {
        $config->setOptimizerTempDir($tempDir);
    }
    $tree = new Mobi_Mtld_DA_Device_TreeOptimized($config);
    $tree->clearCache();
}

/**
 * Analyse speed and memory performance before.
 *
 * @param string tempDir=null If you manually set the temp directory to the API
 *        then pass it to this function
 */
function analysePerformance($jsonDataFilePath, $tempDir=null) {
    try {
        $start     = microtime(true);
        $deviceApi = new Mobi_Mtld_DA_Device_DeviceApi();
        $deviceApi->loadDataFromFile($jsonDataFilePath);
        foreach (getUas() as $ua) {
            $properties = $deviceApi->getProperties($ua);
        }
        $end = microtime(true);

        print
            $GLOBALS['messages'][0]."\n".
            "\n*** Testing ".count(getUas())." user-agents\n".
            "*** No optimization:\n".
            "\ttime taken:   ". sprintf("%0.6f", $end - $start). "s\n".
            "\tmemory usage: ". sprintf("%0.2f",memory_get_peak_usage(true)/(1024*1024)). "MB\n";

        // create cache files
        optimizeDataFile($jsonDataFilePath, $tempDir);
        // test the optimized in a new task
        system('php '.__FILE__.' -b '.$jsonDataFilePath.($tempDir? ' -t '.$tempDir: ''));

    } catch (Mobi_Mtld_DA_Exception_DataFileException $x) {
        print "*** Error: the data file path is not correct!\n";
    } catch (Exception $x) {
        print "*** Error: ".$x->getMessage()."\n";
    }
}

function analyseOptimizedPerformance($jsonDataFilePath, $tempDir=null) {
    try {
        $start  = microtime(true);
        $config = new Mobi_Mtld_DA_Device_Config();
        $config->setUseTreeOptimizer(true);
        if ($tempDir) {
            $config->setOptimizerTempDir($tempDir);
        }
        $deviceApi = new Mobi_Mtld_DA_Device_DeviceApi($config);
        $deviceApi->loadDataFromFile($jsonDataFilePath);
        foreach (getUas() as $ua) {
            $properties = $deviceApi->getProperties($ua);
        }
        $end = microtime(true);

        print
            "*** Using optimized data:\n".
            "\ttime taken:   ". sprintf("%0.6f", $end - $start). "s\n".
            "\tmemory usage: ". sprintf("%0.2f", memory_get_peak_usage(true)/(1024*1024)). "MB\n\n";

    } catch (Mobi_Mtld_DA_Exception_DataFileException $x) {
        print "*** Error: the data file path is not correct!\n";
    } catch (Exception $x) {
        print "*** Error: ".$x->getMessage()."\n";
    }
}

function getUas() {
    return array(
        "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; en-gb) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5",
        "Mozilla/5.0 (Linux; Android 4.2; Nexus 4 Build/JVP15Q) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19",
        "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36",
    );
}




$messages = array(
    "> > > \nDeviceAtlas DeviceApi data file optimizer",
    array(
        "\nUsage:",
        "\nto clear cache (default configs):",
        "\tphp data-file-optimizer.php -c\n",
        "to clear cache (manually set temp directory):",
        "\tphp data-file-optimizer.php -c /custom/temp-directory",
        "\nto optimize data file (default configs):",
        "\tphp data-file-optimizer.php -d /path/to/datafile.json",
        "\nto optimize data file (manually set temp directory):",
        "\tphp data-file-optimizer.php -d /path/to/datafile.json -t /custom/temp-directory",
        "\nuse -f to overwrite cache if already exists:",
        "\tphp data-file-optimizer.php -f /path/to/datafile.json",
        "\tphp data-file-optimizer.php -f /path/to/datafile.json -t /custom/temp-directory",
        "\nto analyse the speed and memory footprint difference:",
        "\tphp data-file-optimizer.php -a /path/to/datafile.json\n",
        "\tphp data-file-optimizer.php -a /path/to/datafile.json -t /custom/temp-directory\n",
    ),
    "< < < \n",
);

$p1 = isset($argv[1])? trim($argv[1]): null;
$p2 = isset($argv[2])? trim($argv[2]): null;
$p3 = isset($argv[3])? trim($argv[3]): null;
$p4 = isset($argv[4])? trim($argv[4]): null;

if ($p1 === '-b') {
    if ($p3 !== '-t') {
        $p4 = null;
    }
    analyseOptimizedPerformance($p2, $p4);
    exit;

} elseif ($p1 === '-a') {
    if ($p3 !== '-t') {
        $p4 = null;
    }
    if ($p2) {
        analysePerformance($p2, $p4);
        exit;
    }

} elseif ($p1 === '-c') {
    if ($p2) {
        try {
            clearOptimizationCache($p2);
            $messages[1] = "*** Cleared temp and cache files directories.";
        } catch (Mobi_Mtld_DA_Exception_DataFileException $x) {
            $messages[1] = "*** Error: the data file path is not correct!";
        } catch (Exception $x) {
            $messages[1] = "*** Error: ".$x->getMessage();
        }
    }

} elseif ($p1 === '-d' || $p1 === '-f') {
    if ($p3 !== '-t') {
        $p4 = null;
    }
    try {
        optimizeDataFile($p2, $p4, $p1==='-f');
        $messages[1] = "*** Data file optimized and cached, now the DeviceApi will use this cached files.";
    } catch (Mobi_Mtld_DA_Exception_DataFileException $x) {
        $messages[1] = "*** Error: the data file path is not correct!";
    } catch (Exception $x) {
        $messages[1] = "*** Error: ".$x->getMessage();
    }
}

$messages[1] = implode("\n", (array)$messages[1]);
print implode("\n", $messages);
