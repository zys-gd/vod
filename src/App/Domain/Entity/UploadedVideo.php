<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * UploadedVideo
 */
class UploadedVideo implements HasUuid
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
     * @var Subcategory
     */
    private $subcategory;

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
    private $createdDate;

    /**
     * @var \DateTime
     */
    private $expiredDate;

    /**
     * @var array
     */
    private $thumbnails = [];

    /**
     * @var VideoPartner
     */
    private $videoPartner;

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
        $this->createdDate = new \DateTime('now');
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
     * @return Subcategory
     */
    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    /**
     * @param Subcategory $subcategory
     *
     * @return UploadedVideo
     */
    public function setSubcategory(?Subcategory $subcategory): UploadedVideo
    {
        $this->subcategory = $subcategory;

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
    public function getCreatedDate(): \DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return UploadedVideo
     */
    public function setCreatedDate(\DateTime $createdAt): UploadedVideo
    {
        $this->createdDate = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiredDate(): ?\DateTime
    {
        return $this->expiredDate;
    }

    /**
     * @param \DateTime $expiredDate
     *
     * @return UploadedVideo
     */
    public function setExpiredDate(?\DateTime $expiredDate): UploadedVideo
    {
        $this->expiredDate = $expiredDate;

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
     *
     * @return UploadedVideo
     */
    public function setDescription(?string $description): UploadedVideo
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @param VideoPartner $videoPartner
     *
     * @return UploadedVideo
     */
    public function setVideoPartner(VideoPartner $videoPartner): UploadedVideo
    {
        $this->videoPartner = $videoPartner;

        return $this;
    }

    /**
     * @return VideoPartner
     */
    public function getVideoPartner(): VideoPartner
    {
        return $this->videoPartner;
    }

    /**
     * @return array
     */
    public function getDataFormTemplate(): array
    {
        return [
            'uuid'       => $this->getUuid(),
            'title'      => $this->getTitle(),
            'publicId'   => $this->getRemoteId(),
            'thumbnails' => $this->getThumbnails()
        ];
    }
}