<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;
use App\Domain\Service\VideoProcessing\DTO\UploadResult;
use App\Utils\UuidGenerator;
use Symfony\Component\Routing\RouterInterface;

class VideoUploader
{
    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $host;

    /**
     * VideoUploader constructor
     *
     * @param CloudinaryConnector $cloudinaryConnector
     * @param RouterInterface $router
     * @param string $host
     */
    public function __construct(
        CloudinaryConnector $cloudinaryConnector,
        RouterInterface $router,
        string $host
    ) {
        $this->cloudinaryConnector = $cloudinaryConnector;
        $this->router              = $router;
        $this->host                = $host;
    }

    /**
     * @param string $realPath
     * @param string $uploadedFolder
     *
     * @return UploadResult
     *
     * @throws \Exception
     */
    public function upload(string $realPath, string $uploadedFolder): UploadResult
    {
        $result = $this->cloudinaryConnector->uploadVideo(
            UuidGenerator::generate(),
            $realPath,
            $uploadedFolder,
            'http://' . $this->host . $this->router->generate('vod_listen', [])
        );

        $uploadResult = new UploadResult($result['public_id']);
        $thumbnails = $this->cloudinaryConnector->getThumbnails($result['public_id']);

        $uploadResult
            ->setRemoteUrl($result['url'])
            ->setThumbnailsPath($thumbnails);

        return $uploadResult;
    }
}