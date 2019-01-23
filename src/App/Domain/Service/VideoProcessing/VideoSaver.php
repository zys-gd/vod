<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Entity\Category;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\DTO\UploadResult;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManager;

class VideoSaver
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VideoSaver constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UploadResult $uploadResult
     * @param Category $category
     * @param string $title
     * @param string $description
     *
     * @return UploadedVideo
     *
     * @throws \Exception
     */
    public function getUploadedVideoInstance(
        UploadResult $uploadResult,
        Category $category,
        string $title,
        ?string $description
    ): UploadedVideo
    {
        $uploadedVideo = new UploadedVideo(UuidGenerator::generate());

        $uploadedVideo
            ->setTitle($title)
            ->setDescription($description)
            ->setCategory($category)
            ->setRemoteUrl($uploadResult->getRemoteUrl())
            ->setRemoteId($uploadResult->getRemoteId())
            ->setThumbnails($uploadResult->getThumbnailsPath());

        return $uploadedVideo;
    }

    /**
     * @param UploadedVideo $uploadedVideo
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function persist(UploadedVideo $uploadedVideo)
    {
        $this->entityManager->persist($uploadedVideo);
        $this->entityManager->flush();
    }
}