<?php

namespace App\Domain\Entity;

use DeviceDetectionBundle\Service\Device;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class GameBuild
 */
class GameBuild implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var integer
     */
    private $osType;

    /**
     * @var string
     */
    private $minOsVersion;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var Collection
     */
    private $deviceDisplays;

    /**
     * @var mixed|UploadedFile
     */
    private $gameApk;

    /**
     * @var integer
     */
    private $apkSize;

    /**
     * @var string
     */
    private $apkVersion;

    /**
     * apk version for drm server
     * @var integer
     */
    private $version;

    /**
     * GameBuild constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->deviceDisplays = new ArrayCollection();
    }

    /**
     * Generate title for entity.
     *
     * @return string
     */
    public function __toString()
    {
        $osTypes = Device::getAvailableOsTypes();

        return !empty($this->getOsType()) ? $osTypes[$this->getOsType()] . ' ' . $this->getMinOsVersion() : '';
    }

    /**
     * Returns a list with all available tags
     *
     * @param bool $flip
     *
     * @return array
     */
    public static function getAvailableOsTypes($flip = false)
    {
        return Device::getAvailableOsTypes($flip);
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
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
     * Set osType
     *
     * @param integer $osType
     *
     * @return GameBuild
     */
    public function setOsType($osType)
    {
        $this->osType = $osType;

        return $this;
    }

    /**
     * Get osType
     *
     * @return integer
     */
    public function getOsType()
    {
        return $this->osType;
    }

    /**
     * Set minOsVersion
     *
     * @param string $minOsVersion
     *
     * @return GameBuild
     */
    public function setMinOsVersion($minOsVersion)
    {
        $this->minOsVersion = $minOsVersion;

        return $this;
    }

    /**
     * Get minOsVersion
     *
     * @return string
     */
    public function getMinOsVersion()
    {
        return $this->minOsVersion;
    }

    /**
     * Set game
     *
     * @param Game $game
     *
     * @return GameBuild
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Add deviceDisplay
     *
     * @param DeviceDisplay $deviceDisplay
     *
     * @return GameBuild
     */
    public function addDeviceDisplay(DeviceDisplay $deviceDisplay)
    {
        $this->deviceDisplays[] = $deviceDisplay;

        return $this;
    }

    /**
     * Remove deviceDisplay
     *
     * @param DeviceDisplay $deviceDisplay
     */
    public function removeDeviceDisplay(DeviceDisplay $deviceDisplay)
    {
        $this->deviceDisplays->removeElement($deviceDisplay);
    }

    /**
     * Get deviceDisplays
     *
     * @return Collection
     */
    public function getDeviceDisplays()
    {
        return $this->deviceDisplays;
    }

    /**
     * @return string|UploadedFile
     */
    public function getGameApk()
    {
        return $this->gameApk;
    }

    /**
     * @param string|UploadedFile $gameApk
     */
    public function setGameApk($gameApk)
    {
        $this->gameApk = $gameApk;
    }

    /**
     * @return int
     */
    public function getApkSize()
    {
        return $this->apkSize;
    }

    /**
     * @param int
     */
    public function setApkSize($apkSize)
    {
        $this->apkSize = $apkSize;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Update apk DRM version
     *
     * @return int
     */
    public function updateVersion()
    {
        if (!$this->version) {
            $this->version = 1;
        } else {
            $this->version += 1;
        };

        return $this->version;
    }

    /**
     * @return string|null
     */
    public function getApkVersion(): ?string
    {
        return $this->apkVersion;
    }

    /**
     * @param string $apkVersion
     *
     * @return GameBuild
     */
    public function setApkVersion(string $apkVersion): self
    {
        $this->apkVersion = $apkVersion;

        return $this;
    }
}
