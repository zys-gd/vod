<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 27.07.18
 * Time: 14:24
 */

class SQSSaver implements Xhgui_Saver_Interface
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $secret;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $version;
    /**
     * @var string
     */
    private $s3bucketName;
    /**
     * @var string
     */
    private $queueName;


    /**
     * SQSSaver constructor.
     */
    public function __construct(string $key, string $secret, string $region, string $version, string $s3bucketName, string $queueName)
    {
        $this->key          = $key;
        $this->secret       = $secret;
        $this->region       = $region;
        $this->version      = $version;
        $this->s3bucketName = $s3bucketName;
        $this->queueName    = $queueName;
    }

    public function save(array $data)
    {


        $config = new \AwsExtended\Config(
            [
                'credentials' => [
                    'key'    => $this->key,
                    'secret' => $this->secret,
                ],
                'version'     => $this->version,
                'region'      => $this->region
            ],
            $this->s3bucketName,
            $this->queueName
        );

        $client = new AwsExtended\SqsClient($config);

        $client->sendMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );


    }
}