<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\DTO\UploadResult;
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
     * @param UploadedVideo $uploadedVideo
     * @param array $options
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persist(
        UploadResult $uploadResult,
        UploadedVideo $uploadedVideo,
        array $options
    ) {
        $preparedOptions = array_filter($options, function ($value) {
            return !empty($value);
        });

        $uploadedVideo
            ->setRemoteUrl($uploadResult->getRemoteUrl())
            ->setRemoteId($uploadResult->getRemoteId())
            ->setThumbnails($uploadResult->getThumbnailsPath())
            ->setOptions($preparedOptions);

        $this->entityManager->persist($uploadedVideo);
        $this->entityManager->flush();
    }
}