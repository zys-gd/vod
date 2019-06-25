<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * UploadedVideo
 */
class UploadedVideo implements HasUuid
{
    /**
     * Video statuses
     */
    const STATUS_IN_PROCESSING = 1;
    const STATUS_TRANSFORMATION_READY = 2;
    const STATUS_CONFIRMED_BY_ADMIN = 3;
    const STATUS_READY = 4;

    /**
     * Statuses array
     */
    const STATUSES = [
        self::STATUS_IN_PROCESSING => 'Processing',
        self::STATUS_TRANSFORMATION_READY => 'Transformation ready',
        self::STATUS_CONFIRMED_BY_ADMIN => 'Confirmed by admin',
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
     * @var array
     */
    private $options = [];

    /**
     * @var VideoPartner
     */
    private $videoPartner;

    /** @var bool */
    private $pause = false;

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
    public function __toString()
    {
        return $this->title ?? '';
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
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
     * @param int $status
     *
     * @return UploadedVideo
     */
    public function updateStatus(int $status): UploadedVideo
    {
        if ($this->getStatus() === self::STATUS_READY) {
            return $this;
        }

        if ($status === self::STATUS_TRANSFORMATION_READY || $status === self::STATUS_CONFIRMED_BY_ADMIN) {
            $status = $this->getStatus() !== self::STATUS_IN_PROCESSING ? self::STATUS_READY : $status;
        }

        $this->setStatus($status);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemoteUrl(): ?string
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
     * @return string|null
     */
    public function getRemoteId(): ?string
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
     * @return array|null
     */
    public function getThumbnails(): ?array
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
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return UploadedVideo
     */
    public function setOptions(array $options): UploadedVideo
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return UploadedVideo
     */
    public function addOption(string $name, string $value): UploadedVideo
    {
        $this->options[$name] = $value;

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
     * @return VideoPartner|null
     */
    public function getVideoPartner(): ?VideoPartner
    {
        return $this->videoPartner;
    }

    /**
     * @return bool
     */
    public function isPause(): bool
    {
        return $this->pause;
    }

    /**
     * @param bool $pause
     */
    public function setPause(bool $pause): void
    {
        $this->pause = $pause;
    }

}