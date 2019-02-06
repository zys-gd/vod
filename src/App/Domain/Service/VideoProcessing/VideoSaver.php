<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Entity\Subcategory;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\DTO\UploadResult;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManager;

/**
 * Class VideoSaver
 */
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
     * @param Subcategory $subcategory
     * @param string $title
     * @param string $description
     *
     * @return UploadedVideo
     *
     * @throws \Exception
     */
    public function getUploadedVideoInstance(
        UploadResult $uploadResult,
        Subcategory $subcategory,
        string $title,
        ?string $description
    ): UploadedVideo
    {
        /** @var UploadedVideo $uploadedVideo */
        $uploadedVideo = new UploadedVideo(UuidGenerator::generate());

        $uploadedVideo
            ->setTitle($title)
            ->setDescription($description)
            ->setSubcategory($subcategory)
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