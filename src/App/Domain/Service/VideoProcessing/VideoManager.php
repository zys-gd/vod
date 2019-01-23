<?php

namespace App\Domain\Service\VideoProcessing;

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
     * @param string $title
     * @param string $description
     *
     * @throws \Exception
     */
    public function persistUploadedVideo(UploadResult $uploadResult, string $title, string $description)
    {
        $uploadedVideo = $this->saver->getUploadedVideoInstance($uploadResult, $title, $description);

        $this->saver->persist($uploadedVideo);
    }

    /**
     * @param UploadedFile $file
     * @param string $remoteFolder
     *
     * @return UploadResult
     *
     * @throws \Exception
     */
    public function uploadVideoFileToStorage(UploadedFile $file, string $remoteFolder): UploadResult
    {
        if ($file->getError()) {
            throw new \Exception($file->getErrorMessage());
        }

        return $this->uploader->upload($file->getRealPath(), $remoteFolder);
    }

    /**
     * @param UploadedVideo $uploadedVideo
     */
    public function destroyUploadedVideo(UploadedVideo $uploadedVideo)
    {
        $this->destroyer->destroy($uploadedVideo->getRemoteId());
    }
}