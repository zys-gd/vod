<?php

namespace App\Domain\Service\VideoProcessing\DTO;

/**
 * Class UploadResult
 */
class UploadResult
{
    /**
     * @var string
     */
    private $remoteUrl;

    /**
     * @var string
     */
    private $remoteId;

    /**
     * @var array
     */
    private $thumbnailsPath;

    /**
     * UploadResult constructor
     *
     * @param string $remoteId
     */
    public function __construct(string $remoteId)
    {
        $this->remoteId = $remoteId;
    }

    /**
     * @return string
     */
    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    /**
     * @return string
     */
    public function getRemoteUrl(): string
    {
        return $this->remoteUrl;
    }

    /**
     * @param string $remoteUrl
     *
     * @return UploadResult
     */
    public function setRemoteUrl(string $remoteUrl): self
    {
        $this->remoteUrl = $remoteUrl;

        return $this;
    }

    /**
     * @return array
     */
    public function getThumbnailsPath(): array
    {
        return $this->thumbnailsPath;
    }

    /**
     * @param array $thumbnailsPath
     *
     * @return UploadResult
     */
    public function setThumbnailsPath(array $thumbnailsPath): self
    {
        $this->thumbnailsPath = $thumbnailsPath;

        return $this;
    }
}