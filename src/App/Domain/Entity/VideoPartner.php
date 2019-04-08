<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class VideoPartner
 */
class VideoPartner
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Collection
     */
    private $uploadedVideos;

    /**
     * VideoPartner constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->uploadedVideos = new ArrayCollection();
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
     *
     * @return VideoPartner
     */
    public function setUuid(string $uuid): VideoPartner
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return VideoPartner
     */
    public function setName(string $name): VideoPartner
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUploadedVideos(): Collection
    {
        return $this->uploadedVideos;
    }

    /**
     * @param Collection $uploadedVideos
     *
     * @return VideoPartner
     */
    public function setUploadedVideos(Collection $uploadedVideos): VideoPartner
    {
        /** @var UploadedVideo $affiliateParameter */
        foreach ($uploadedVideos->getIterator() as $uploadedVideo) {
            $this->addUploadedVideo($uploadedVideo);
        }

        return $this;
    }

    /**
     * @param UploadedVideo $uploadedVideo
     *
     * @return VideoPartner
     */
    public function addUploadedVideo(UploadedVideo $uploadedVideo): VideoPartner
    {
        if (!$this->uploadedVideos->contains($uploadedVideo)) {
            $uploadedVideo->setVideoPartner($this);
            $this->uploadedVideos->add($uploadedVideo);
        }

        return $this;
    }

    /**
     * @param UploadedVideo $uploadedVideo
     *
     * @return VideoPartner
     */
    public function removeUploadedVideo(UploadedVideo $uploadedVideo): VideoPartner
    {
        if ($this->uploadedVideos->contains($uploadedVideo)) {
            $this->uploadedVideos->removeElement($uploadedVideo);

            if ($uploadedVideo->getVideoPartner() === $this) {
                $uploadedVideo->setVideoPartner(null);
            }
        }

        return $this;
    }
}