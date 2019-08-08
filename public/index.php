<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;


require dirname(__DIR__) . '/config/bootstrap.php';


if (isset($_COOKIE['SNOOKER_IN_COLOMBO'])) {
    $_SERVER['APP_ENV']   = $_ENV['APP_ENV'] = $_COOKIE['SNOOKER_IN_COLOMBO'];
    $_SERVER['APP_DEBUG'] = (in_array($_SERVER['APP_ENV'], ['dev', 'stage_debug']));
}


if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    Debug::enable();
}

require_once __DIR__ . '/../profiler/include.php';

Request::setTrustedProxies(
    [$_SERVER['REMOTE_ADDR']],
    Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST
);

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}


$kernel = new VODKernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);

$request = Request::createFromGlobals();

try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $exception) {

    $log = sprintf(
        'Uncaught PHP Exception %s: "%s" at %s line %s',
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );

    if ($_SERVER['APP_DEBUG']) {
        http_response_code(500);
        echo $exception->getMessage();
        error_log($log);
    } else {
        error_log($log);
        throw $exception;
    }
}

