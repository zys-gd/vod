<?php

namespace App\Domain\Service\VideoProcessing\Connectors;

/**
 * Class CloudinaryConnector
 */
class CloudinaryConnector
{
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
     * Upload video to cloudinary storage
     *
     * @param string $alias
     * @param string $filePath
     * @param string $folderName
     * @param string $callbackUrl
     *
     * @return array
     *
     * @throws \Exception
     */
    public function uploadVideo(string $alias, string $filePath, string $folderName, string $callbackUrl): array
    {
        $options = [
            'eager'                  => [['streaming_profile' => 'hd', 'format' => 'm3u8']],
            'eager_async'            => true,
            'eager_notification_url' => $callbackUrl,
            'cloud_name'             => $this->cloudName,
            'api_key'                => $this->apiKey,
            'api_secret'             => $this->apiSecret,
            'folder'                 => $folderName,
            'public_id'              => $alias,
            'overwrite'              => true,
            'resource_type'          => 'video'
        ];

        return \Cloudinary\Uploader::upload_large($filePath, $options);
    }

    /**
     * @param string $publicId
     *
     * @return array
     */
    public function getThumbnails(string $publicId): array
    {
        $transformation = [['width' => 250, 'crop' => 'scale']];

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