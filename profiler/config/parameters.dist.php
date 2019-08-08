<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 27.07.18
 * Time: 11:41
 */

return [
    'XHGUI_USE_REMOTE_STORAGE' => $_SERVER['XHGUI_USE_REMOTE_STORAGE'] ?? false,
    'XHGUI_PROFILING'          => $_SERVER['XHGUI_PROFILING'] ?? false,
    'XHGUI_PROFILING_RATIO'    => $_SERVER['XHGUI_PROFILING_RATIO'] ?? 100,

    'XHGUI_AWS_KEY'            => $_SERVER['XHGUI_AWS_KEY'] ?? '',
    'XHGUI_AWS_SECRET'         => $_SERVER['XHGUI_AWS_SECRET'] ?? '',
    'XHGUI_AWS_REGION'         => $_SERVER['XHGUI_AWS_REGION'] ?? '',
    'XHGUI_AWS_VERSION'        => $_SERVER['XHGUI_AWS_VERSION'] ?? '',
    'XHGUI_AWS_S3_BUCKETNAME'  => $_SERVER['XHGUI_AWS_S3_BUCKETNAME'] ?? '',
    'XHGUI_AWS_SQS_QUEUE_NAME' => $_SERVER['XHGUI_AWS_SQS_QUEUE_NAME'] ?? ''

];