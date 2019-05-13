<?php


namespace App\Domain\DTO;


use App\Domain\Entity\UploadedVideo;

class BatchOfNotExpiredVideos
{
    /**
     * @var UploadedVideo[]|array
     */
    private $videos;
    /**
     * @var bool
     */
    private $isLast;

    /**
     * BatchOfNotExpiredVideos constructor.
     * @param UploadedVideo[]  $videos
     * @param bool  $isLast
     */
    public function __construct(array $videos, bool $isLast)
    {
        $this->videos = $videos;
        $this->isLast = $isLast;
    }

    /**
     * @return UploadedVideo[]
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    /**
     * @return bool
     */
    public function isLast(): bool
    {
        return $this->isLast;
    }
}