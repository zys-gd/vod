<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.18
 * Time: 16:39
 */

namespace App\Twig;



use App\Domain\Entity\UploadedVideo;

class VideoProcessingHelperExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $cloudName;


    /**
     * VideoProcessingHelperExtension constructor.
     */
    public function __construct(string $cloudName)
    {
        $this->cloudName = $cloudName;
    }

    public function getFunctions()
    {

        return [
            new \Twig_SimpleFunction('isVideoReady', function (UploadedVideo $uploadedVideo) {
                return $uploadedVideo->getStatus() === UploadedVideo::STATUS_READY;
            }),
            new \Twig_SimpleFunction('getCloudName', function () {
                return $this->cloudName;
            }),
            new \Twig_SimpleFunction('createPlaylistElement', function (UploadedVideo $uploadedVideo) {

                return json_encode([
                    'publicId' => $uploadedVideo->getRemoteId(),
                    'info'     => [
                        'title' => $uploadedVideo->getTitle(),
                    ]
                ]);
            })
        ];

    }
}