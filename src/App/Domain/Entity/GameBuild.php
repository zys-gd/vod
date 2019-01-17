<?php

namespace App\Domain\Entity;

use DeviceDetectionBundle\Service\Device;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class GameBuild
 * @package App\Domain\Entity
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
    private $os_type;

    /**
     * @var string
     */
    private $min_os_version;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var Collection
     */
    private $device_displays;

    /**
     * @var mixed|UploadedFile
     */
    private $game_apk;

    /**
     * @var integer
     */
    private $apk_size;

    private $apk_version;

    /**
     * apk version for drm server
     * @var integer
     */
    private $version;

    /**
     * Returns a list with all available tags
     *
     * @param bool $flip
     * @return array
     */
    public static function getAvailableOsTypes($flip = false)
    {
        return Device::getAvailableOsTypes($flip);
    }

    /**
     * GameBuild constructor.
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->device_displays = new ArrayCollection();
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
     * Generate title for entity.
     *
     * @return string
     */
    public function __toString()
    {
        $osTypes = Device::getAvailableOsTypes();

        return $osTypes[$this->getOsType()] . ' ' . $this->getMinOsVersion();
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
        $this->os_type = $osType;

        return $this;
    }

    /**
     * Get osType
     *
     * @return integer
     */
    public function getOsType()
    {
        return $this->os_type;
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
        $this->min_os_version = $minOsVersion;

        return $this;
    }

    /**
     * Get minOsVersion
     *
     * @return string
     */
    public function getMinOsVersion()
    {
        return $this->min_os_version;
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
        $this->device_displays[] = $deviceDisplay;

        return $this;
    }

    /**
     * Remove deviceDisplay
     *
     * @param DeviceDisplay $deviceDisplay
     */
    public function removeDeviceDisplay(DeviceDisplay $deviceDisplay)
    {
        $this->device_displays->removeElement($deviceDisplay);
    }

    /**
     * Get deviceDisplays
     *
     * @return Collection
     */
    public function getDeviceDisplays()
    {
        return $this->device_displays;
    }

    /**
     * @return string|UploadedFile
     */
    public function getGameApk()
    {
        return $this->game_apk;
    }

    /**
     * @param string|UploadedFile $gameApk
     */
    public function setGameApk($gameApk)
    {
        $this->game_apk = $gameApk;
    }

    /**
     * @return int
     */
    public function getApkSize()
    {
        return $this->apk_size;
    }

    /**
     * @param int
     */
    public function setApkSize($apk_size)
    {
        $this->apk_size = $apk_size;
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
     * @return int
     */
    public function updateVersion()
    {
        if (!$this->version){
            $this->version = 1;
        }
        else{
            $this->version += 1;
        };
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getApkVersion()
    {
        return $this->apk_version;
    }

    /**
     * @param mixed $apk_version
     * @return GameBuild
     */
    public function setApkVersion($apk_version)
    {
        $this->apk_version = $apk_version;
        return $this;
    }


}
