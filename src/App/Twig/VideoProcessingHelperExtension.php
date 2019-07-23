<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.18
 * Time: 16:39
 */

namespace App\Twig;



use App\Domain\Entity\UploadedVideo;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VideoProcessingHelperExtension extends AbstractExtension
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
            new TwigFunction('isVideoReady', function (UploadedVideo $uploadedVideo) {
                return $uploadedVideo->getStatus() === UploadedVideo::STATUS_READY;
            }),
            new TwigFunction('getCloudName', function () {
                return $this->cloudName;
            }),
            new TwigFunction('createPlaylistElement', function (UploadedVideo $uploadedVideo) {

                return json_encode(array_merge(
                    [
                        'publicId' => $uploadedVideo->getRemoteId(),
                        'info'     => [
                            'title' => $uploadedVideo->getTitle(),
                        ]
                    ],
                    $uploadedVideo->getOptions() ? $uploadedVideo->getOptions() : []
                ));
            })
        ];

    }
}