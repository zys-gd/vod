<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.12.18
 * Time: 11:40
 */

namespace App\Domain\Service\VideoProcessing\Connectors;


use App\Domain\Service\VideoProcessing\DTO\UploadResult;

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
     * CloudinaryConnector constructor.
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

    public function makeSignature(string $payload): string
    {
        $sign = sha1($payload . $this->apiSecret);

        return $sign;

    }

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

        return new UploadResult($result['url'], $result['public_id']);

    }

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
     * Delete video from cloudinary storage
     *
     * @param $remoteId
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