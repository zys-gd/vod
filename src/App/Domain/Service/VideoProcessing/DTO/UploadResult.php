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
    private $url;

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
     * @param string $url
     * @param string $remoteId
     * @param array $thumbnailsPath
     */
    public function __construct(string $url, string $remoteId, array $thumbnailsPath)
    {
        $this->url      = $url;
        $this->remoteId = $remoteId;
        $this->thumbnailsPath = $thumbnailsPath;
    }

    /**
     * @return string
     */
    public function getRemoteUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    /**
     * @return array
     */
    public function getThumbnailsPath(): array
    {
        return $this->thumbnailsPath;
    }
}