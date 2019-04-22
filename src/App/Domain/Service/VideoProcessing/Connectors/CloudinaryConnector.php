<?php

namespace App\Domain\Service\VideoProcessing\Connectors;

/**
 * Class CloudinaryConnector
 */
class CloudinaryConnector
{
    const SUCCESS_DESTROY_RESULT = 'ok';
    const NOT_FOUND_DESTROY_RESULT = 'not found';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var string
     */
    private $cloudName;

    /**
     * CloudinaryConnector constructor
     *
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $cloudName
     */
    public function __construct(string $apiKey, string $apiSecret, string $cloudName)
    {
        $this->apiKey    = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->cloudName = $cloudName;
    }

    /**
     * @param string $payload
     *
     * @return string
     */
    public function makeSignature(string $payload): string
    {
        $sign = sha1($payload . $this->apiSecret);

        return $sign;
    }

    /**
     * @param string $publicId
     *
     * @return array
     */
    public function getThumbnails(string $publicId): array
    {
        $transformation = [['width' => 375, 'crop' => 'scale']];

        return [
            cl_video_thumbnail_path($publicId, [
                'cloud_name' => $this->cloudName,
                'transformation' => $transformation,
                'start_offset' => '25%'
            ]),
            cl_video_thumbnail_path($publicId, [
                'cloud_name' => $this->cloudName,
                'transformation' => $transformation,
                'start_offset' => '50%'
            ]),
            cl_video_thumbnail_path($publicId, [
                'cloud_name' => $this->cloudName,
                'transformation' => $transformation,
                'start_offset' => '75%'
            ])
        ];
    }

    /**
     * @param $remoteId
     *
     * @return mixed
     */
    public function destroyVideo($remoteId)
    {
        $options = [
            'resource_type' => 'video',
            'cloud_name'    => $this->cloudName,
            'api_key'       => $this->apiKey,
            'api_secret'    => $this->apiSecret,
        ];

        return \Cloudinary\Uploader::destroy($remoteId, $options);
    }
}