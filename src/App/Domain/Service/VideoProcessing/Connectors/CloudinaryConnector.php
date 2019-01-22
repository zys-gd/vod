<?php

namespace App\Domain\Service\VideoProcessing\Connectors;

use App\Domain\Service\VideoProcessing\DTO\UploadResult;

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
     * @param string $src
     * @param string $folderName
     * @param string $callbackUrl
     *
     * @return UploadResult
     *
     * @throws \Exception
     */
    public function uploadVideo(string $alias, string $src, string $folderName, string $callbackUrl): UploadResult
    {
        $result = \Cloudinary\Uploader::upload_large($src,
            [
                "eager"                  => [
                    ["streaming_profile" => "hd", "format" => "m3u8"],
                ],
                "eager_async"            => true,
                "eager_notification_url" => $callbackUrl,
                'cloud_name'             => $this->cloudName,
                'api_key'                => $this->apiKey,
                'api_secret'             => $this->apiSecret,
                "folder"                 => $folderName,
                "public_id"              => $alias,
                "overwrite"              => TRUE,
                "resource_type"          => "video"
            ]
        );

        return new UploadResult($result['url'], $result['public_id'], $this->getThumbnails($result['public_id']));

    }

    /**
     * @param string $publicId
     *
     * @return array
     */
    public function getThumbnails(string $publicId): array
    {
        $transformation = [
            array("width" => 250, "crop" => "scale")
        ];
        return [
            cl_video_thumbnail_path($publicId, ['cloud_name' => $this->cloudName, 'transformation' => $transformation, 'start_offset' => '25%']),
            cl_video_thumbnail_path($publicId, ['cloud_name' => $this->cloudName, 'transformation' => $transformation, 'start_offset' => '50%']),
            cl_video_thumbnail_path($publicId, ['cloud_name' => $this->cloudName, 'transformation' => $transformation, 'start_offset' => '75%'])
        ];
    }

    /**
     * @param $remoteId
     *
     * @return mixed
     */
    public function deleteVideo($remoteId)
    {
        $result = \Cloudinary\Uploader::destroy(
            $remoteId,
            [
                'resource_type' => 'video',
                'cloud_name'    => $this->cloudName,
                'api_key'       => $this->apiKey,
                'api_secret'    => $this->apiSecret,
            ]
        );

        return $result;
    }
}