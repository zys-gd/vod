<?php

namespace App\Domain\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UploadedVideo
 */
class UploadedVideo
{
    /**
     * transformation video statuses
     */
    const STATUS_IN_PROCESSING = 1;
    const STATUS_READY = 2;

    /**
     * Statuses array
     */
    const STATUSES = [
        self::STATUS_IN_PROCESSING => 'Processing',
        self::STATUS_READY => 'Ready'
    ];

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $description;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var int
     */
    private $status = self::STATUS_IN_PROCESSING;

    /**
     * @var string
     */
    private $remoteUrl;

    /**
     * @var string
     */
    private $remoteId;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var array
     */
    private $thumbnails = [];

    /**
     * Unmapped field for uploading video file
     */
    private $videoFile;

    /**
     * UploadedVideo constructor
     *
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return UploadedVideo
     */
    public function setTitle($title): UploadedVideo
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return UploadedVideo
     */
    public function setCategory(Category $category): UploadedVideo
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return UploadedVideo
     */
    public function setStatus(int $status): UploadedVideo
    {
        $this->status = $status;

        return $this;
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
     * @return UploadedVideo
     */
    public function setRemoteUrl(string $remoteUrl): UploadedVideo
    {
        $this->remoteUrl = $remoteUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    /**
     * @param string $remoteId
     *
     * @return UploadedVideo
     */
    public function setRemoteId(string $remoteId): UploadedVideo
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return UploadedVideo
     */
    public function setCreatedAt(\DateTime $createdAt): UploadedVideo
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    /**
     * @param array $thumbnails
     *
     * @return UploadedVideo
     */
    public function setThumbnails(array $thumbnails): UploadedVideo
    {
        $this->thumbnails = $thumbnails;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param UploadedFile|null $videoFile
     *
     * @return UploadedVideo
     */
    public function setVideoFile(?UploadedFile $videoFile)
    {
        $this->videoFile = $videoFile;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getVideoFile()
    {
        return $this->videoFile;
    }
}