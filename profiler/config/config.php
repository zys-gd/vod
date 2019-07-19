<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.07.18
 * Time: 14:34
 */

if (file_exists(__DIR__ . '/parameters.php')) {
    $parameters = require __DIR__ . '/parameters.php';
} else {
    $parameters = require __DIR__ . '/parameters.dist.php';

}


return [
    'debug' => false,
    'mode'  => 'development',

    'save.handler' => $parameters['XHGUI_USE_REMOTE_STORAGE']
        ? 'sqs'
        : 'file',


    'save.handler.filename'             => __DIR__ . '/../logs/' . 'xhgui.data.' . microtime(true) . '_' . substr(md5(mt_rand(0, 1000)), 0, 6),

    'save.handler.sqs.credentials' => [
        'key'          => $parameters['XHGUI_AWS_KEY'],
        'secret'       => $parameters['XHGUI_AWS_SECRET'],
        'region'       => $parameters['XHGUI_AWS_REGION'],
        'version'      => $parameters['XHGUI_AWS_VERSION'],
        's3BucketName' => $parameters['XHGUI_AWS_S3_BUCKETNAME'],
        'sqsQueueName' => $parameters['XHGUI_AWS_SQS_QUEUE_NAME'],
    ],
    'templates.path'               => dirname(__DIR__) . '/src/templates',
    'date.format'                  => 'M jS H:i:s',
    'detail.count'                 => 6,
    'page.limit'                   => 25,
    'profiler.enable'              => function () use ($parameters) {

        $ratio             = $parameters['XHGUI_PROFILING_RATIO'] ?? 100;
        $rollValue         = mt_rand(0, 100) + (mt_rand(0, 100) / 100);
        $isNeedToBeTracked = isset($_GET['enable_xhprof']) && $_GET['enable_xhprof']
            ? true
            : ($parameters['XHGUI_PROFILING'] !== false) && ($rollValue <= $ratio);


        $_SERVER['XHPROF_REQUEST_IS_TRACKED'] = (int)$isNeedToBeTracked;

        return $isNeedToBeTracked;

    },

    'profiler.simple_url' => function ($url) {
        return preg_replace('/\=\d+/', '', $url);
    },

    'profiler.options' => [],
];