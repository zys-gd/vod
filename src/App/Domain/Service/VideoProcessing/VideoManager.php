<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Entity\Subcategory;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\DTO\UploadResult;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class VideoUploader
 */
class VideoManager
{
    /**
     * @var VideoUploader
     */
    private $uploader;

    /**
     * @var VideoSaver
     */
    private $saver;

    /**
     * @var VideoDestroyer
     */
    private $destroyer;

    /**
     * VideoManager constructor
     *
     * @param VideoUploader $uploader
     * @param VideoSaver $saver
     * @param VideoDestroyer $destroyer
     */
    public function __construct(
        VideoUploader $uploader,
        VideoSaver $saver,
        VideoDestroyer $destroyer
    ) {
        $this->uploader = $uploader;
        $this->saver = $saver;
        $this->destroyer = $destroyer;
    }

    /**
     * @param UploadResult $uploadResult
     * @param UploadedVideo $uploadedVideo
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function persistUploadedVideo(
        UploadResult $uploadResult,
        UploadedVideo $uploadedVideo
    ) {
        $this->saver->persist($uploadResult, $uploadedVideo);
    }

    /**
     * @param UploadedFile $file
     * @param string $remoteFolder
     * @param array $options
     *
     * @return UploadResult
     *
     * @throws \Exception
     */
    public function uploadVideoFileToStorage(UploadedFile $file, string $remoteFolder, array $options): UploadResult
    {
        if ($file->getError()) {
            throw new \Exception($file->getErrorMessage());
        }

        return $this->uploader->upload($file->getRealPath(), $remoteFolder, $options);
    }

    /**
     * @param UploadedVideo $uploadedVideo
     *
     * @return mixed
     */
    public function destroyUploadedVideo(UploadedVideo $uploadedVideo)
    {
        return $this->destroyer->destroy($uploadedVideo->getRemoteId());
    }
}