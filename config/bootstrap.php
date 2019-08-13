<?php


require dirname(__DIR__) . '/vendor/autoload.php';


if (isset($_COOKIE['SNOOKER_IN_COLOMBO'])) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $_COOKIE['SNOOKER_IN_COLOMBO'];
}

if (!isset($_SERVER['APP_ENV']) || !$_SERVER['APP_ENV']) {
    $_SERVER['APP_ENV'] = 'dev';
}

if (!isset($_SERVER['APP_DEBUG'])) {
    $_SERVER['APP_DEBUG'] = in_array($_SERVER['APP_ENV'], ['dev', 'stage_debug', 'ci_dev', 'stage']);
}

if (!isset($_SERVER['APP_SHOW_DEBUG_INFO'])) {
    $_SERVER['APP_SHOW_DEBUG_INFO'] = in_array($_SERVER['APP_ENV'], ['dev', 'stage_debug']);
}
