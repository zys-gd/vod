<?php

namespace App\Domain\Entity;

/**
 * UploadedVideo
 */
class UploadedVideo
{
    const STATUS_IN_PROCESSING = 1;
    const STATUS_READY = 2;

    const STATUSES = [
        self::STATUS_IN_PROCESSING => 'Processing',
        self::STATUS_READY => 'Ready'
    ];
    /**
     * @var string
     */
    private $uuid;


    private $title = '';

    private $category;

    private $status = self::STATUS_IN_PROCESSING;

    private $remoteUrl;

    private $remoteId;

    private $createdAt;

    private $thumbnails = [];

    /**
     * UploadedVideo constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }


    /**
     * Get id
     *
     * @return string
     */
    public function getUuid()
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
     * @return UploadedVideo
     */
    public function setTitle(string $title): UploadedVideo
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return VideoCategory
     */
    public function getCategory(): VideoCategory
    {
        return $this->category;
    }

    /**
     * @param VideoCategory $category
     * @return UploadedVideo
     */
    public function setCategory(VideoCategory $category)
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
     * @return UploadedVideo
     */
    public function setStatus(int $status): UploadedVideo
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRemoteUrl()
    {
        return $this->remoteUrl;
    }

    /**
     * @param mixed $remoteUrl
     * @return UploadedVideo
     */
    public function setRemoteUrl($remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * @param mixed $remoteId
     * @return UploadedVideo
     */
    public function setRemoteId($remoteId)
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
     * @return UploadedVideo
     */
    public function setThumbnails(array $thumbnails): UploadedVideo
    {
        $this->thumbnails = $thumbnails;
        return $this;
    }





}

