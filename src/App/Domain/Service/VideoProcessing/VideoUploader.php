<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Service\VideoProcessing\DTO\UploadResult;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;

/**
 * Class VideoUploader
 */
class VideoUploader
{
    /**
     * @var UploadedVideoRepository
     */
    private $repository;

    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $host;

    /**
     * VideoUploader constructor
     *
     * @param UploadedVideoRepository $repository
     * @param CloudinaryConnector     $cloudinaryConnector
     * @param RouterInterface         $router
     * @param EntityManagerInterface  $entityManager
     * @param string                  $host
     */
    public function __construct(
        UploadedVideoRepository $repository,
        CloudinaryConnector $cloudinaryConnector,
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        string $host
    )
    {
        $this->repository          = $repository;
        $this->cloudinaryConnector = $cloudinaryConnector;
        $this->router              = $router;
        $this->entityManager       = $entityManager;
        $this->host                = $host;
    }

    /**
     * Upload video to cloudinary storage
     *
     * @param UploadedFile $uploadedFile
     * @param string $uploadedFolder
     *
     * @return UploadResult
     *
     * @throws \Exception
     */
    public function uploadVideo(UploadedFile $uploadedFile, string $uploadedFolder): UploadResult
    {
        if ($uploadedFile->getError()) {
            throw new \Exception($uploadedFile->getErrorMessage());
        }

        $result = $this->cloudinaryConnector->uploadVideo(
            UuidGenerator::generate(),
            $uploadedFile->getRealPath(),
            $uploadedFolder,
            'http://' . $this->host . $this->router->generate('vod_listen', [])
        );

        return $result;
    }
}