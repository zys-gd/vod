<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.07.18
 * Time: 13:45
 */

class CustomSaverFactory extends Xhgui_Saver
{
    public static function factory($config): Xhgui_Saver_Interface
    {

        if ($config['save.handler'] === 'file') {
            return new Xhgui_Saver_File($config['save.handler.filename']);
        }

        if ($config['save.handler'] === 'sqs') {

            $credentials = $config['save.handler.sqs.credentials'];
            require_once __DIR__ . '/SQSSaver.php';
            return new SQSSaver(
                $credentials['key'],
                $credentials['secret'],
                $credentials['region'],
                $credentials['version'],
                $credentials['s3BucketName'],
                $credentials['sqsQueueName']
            );
        }

        throw new \InvalidArgumentException('Unsupported save handler');
    }

}