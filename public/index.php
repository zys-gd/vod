<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;


require dirname(__DIR__) . '/config/bootstrap.php';

if (isset($_COOKIE['SNOOKER_IN_COLOMBO'])) {
    $_SERVER['APP_ENV']   = $_ENV['APP_ENV'] = $_COOKIE['SNOOKER_IN_COLOMBO'];
    $_SERVER['APP_DEBUG'] = 1;
}


if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

Request::setTrustedProxies(
    [$_SERVER['REMOTE_ADDR']],
    Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST
);

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}


$kernel  = new VODKernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();

try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $exception) {
    http_response_code(500);
    echo '<pre>'; print_r($exception->getFile()); echo '</pre>';
    echo '<pre>'; print_r($exception->getLine()); echo '</pre>';
    echo '<pre>'; print_r($exception->getTraceAsString()); echo '</pre>';
    echo $exception->getMessage();
}

