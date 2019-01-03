<?php
/*
 *  Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */


/**
 * This class analysis clients connectivity performance by doing a round trip
 * redirect with a fixed size payload of random content then measuring the time.
 * It is a proxy for the current bandwidth and latency.
 */
class Mobi_Mtld_DA_ConnectivityAnalyser {

    // how often to check user connection, one analysis is done after this
    // number of requests meanwhile previously analysis results will be returned
    const FREQUENCY             = 1;

    // payload size and thresholds
    const PAYLOAD_SIZE_BYTES    = 1024;
    const LOW_THRESHOLD_MS      = 900;
    const MEDIUM_THRESHOLD_MS   = 400;

    // connection quality
    const LOW_QUALITY           = 'low';
    const MEDIUM_QUALITY        = 'medium';
    const HIGH_QUALITY          = 'high';

    // session keys
    const START_TIME_PARAM      = '_deviceatlas_analyse_start_time_';
    const REQUESTS_NUMBER_PARAM = '_deviceatlas_analyse_requests_number_';
    const DURATION_PARAM        = '_deviceatlas_analyse_duration_';
    const QUALITY_PARAM         = '_deviceatlas_analyse_quality_';

    private $duration = 0;
    private $quality  = self::HIGH_QUALITY;

    /**
     * Analyse connectivity for the current user.
     */
    public function __construct() {
        $this->analyse();
    }
    /**
     * Get the round trip time (user > server > user) in milliseconds.
     * @return int Duration time in milliseconds
     */
    public function getDuration() {
        return $this->duration;
    }
    /**
     * Get the connectivity quality (low, medium or high).
     * @return string Value of Mobi_Mtld_DA_ConnectivityAnalyser::LOW_QUALITY or
     *                         Mobi_Mtld_DA_ConnectivityAnalyser::MEDIUM_QUALITY or
     *                         Mobi_Mtld_DA_ConnectivityAnalyser::HIGH_QUALITY
     */
    public function getQuality() {
        return $this->quality;
    }
    /**
     * Analyse connectivity
     */
    private function analyse() {
        // bypass
        if (isset($_SESSION[self::REQUESTS_NUMBER_PARAM])
            && $_SESSION[self::REQUESTS_NUMBER_PARAM] < self::FREQUENCY) {

            $_SESSION[self::REQUESTS_NUMBER_PARAM]++;
            $this->duration = $_SESSION[self::DURATION_PARAM];
            $this->quality  = $_SESSION[self::QUALITY_PARAM];

        // first time
        } elseif (!isset($_SESSION[self::START_TIME_PARAM])) {
            $this->start();

        // calculate time
        } else {
            $this->calculate();
        }
    }
    /**
     * Start a round trip request. Create a fixed size payload and perform a
     * 302 redirect with no-cache headers.
     */
    private function start() {
        @ini_set('implicit_flush', 1);
        @apache_setenv('no-gzip', 1);
        header('Location: '.$_SERVER['REQUEST_URI'], true, 302);
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-Length: '.self::PAYLOAD_SIZE_BYTES);
        header('Content-Type: text/html');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
        header('Content-Encoding: none');
        echo $this->createPayLoad();
        $_SESSION[self::START_TIME_PARAM] = microtime(true);
        exit();
    }
    /**
     * Calculate the duration based on the time of the redirect and choose a 
     * low, medium or high quality. This also resets the start time param for 
     * the next page load.
     */
    private function calculate() {
        $endTime   = microtime(true);
        $startTime = $_SESSION[self::START_TIME_PARAM];

        // reset start time and frequancy counter
        unset($_SESSION[self::START_TIME_PARAM]);
        $_SESSION[self::REQUESTS_NUMBER_PARAM] = 1;
        
        // calculate duration
        $duration       = ($endTime - $startTime) * 1000.0;
        $this->duration = $duration;
        $_SESSION[self::DURATION_PARAM] = $duration;

        // calculate quality
        if ($duration > self::LOW_THRESHOLD_MS) {
            $quality = self::LOW_QUALITY;

        } else if ($duration < self::LOW_THRESHOLD_MS
                    && $duration > self::MEDIUM_THRESHOLD_MS) {
            $quality = self::MEDIUM_QUALITY;

        } else {
            $quality = self::HIGH_QUALITY;

        }
        $this->quality = $quality;
        $_SESSION[self::QUALITY_PARAM] = $quality;
    }
    /**
     * Create a random string of length PAYLOAD_SIZE_BYTES.
     */
    private function createPayLoad() {
        $characters =
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($characters) - 1;
        $randomString = '';

        for ($i = 0; $i < self::PAYLOAD_SIZE_BYTES; $i++) {
            $randomString .= $characters[rand(0, $len)];
        }

        return $randomString;
    }
}
