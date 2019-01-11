<?php

namespace App\Domain\Service\VideoProcessing;

use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Entity\Category;
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
     * @param array $uploadedVideoFormData
     *
     * @return UploadedVideo
     *
     * @throws \Exception
     */
    public function uploadVideo($uploadedVideoFormData): UploadedVideo
    {
        /** @var UploadedFile $file */
        $file = $uploadedVideoFormData['file'];

        /** @var Category $category */
        $category = $uploadedVideoFormData['category'];

        if ($file->getError()) {
            throw new \Exception($file->getErrorMessage());
        }

        $folderName = $category->getAlias();

        $result = $this->cloudinaryConnector->uploadVideo(
            UuidGenerator::generate(),
            $file->getRealPath(),
            $folderName,
            'http://' . $this->host . $this->router->generate('vod_listen', [])
        );

        $videoEntity = new UploadedVideo(UuidGenerator::generate());

        $videoEntity->setTitle($uploadedVideoFormData['title']);
        $videoEntity->setCategory($category);
        $videoEntity->setRemoteUrl($result->getUrl());
        $videoEntity->setRemoteId($result->getRemoteId());
        $videoEntity->setDescription($uploadedVideoFormData['description']);

        $thumbnails = $this->cloudinaryConnector->getThumbnails($result->getRemoteId());
        $videoEntity->setThumbnails($thumbnails);


        $this->entityManager->persist($videoEntity);
        $this->entityManager->flush();

        return $videoEntity;
    }
}