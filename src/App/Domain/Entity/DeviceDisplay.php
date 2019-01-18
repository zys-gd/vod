<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class DeviceDisplay
 * @package App\Domain\Entity
 */
class DeviceDisplay implements HasUuid
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * @var Collection
     */
    private $game_builds;

    /** @var string */
    private $uuid = null;

    /**
     * DeviceDisplay constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->game_builds = new ArrayCollection();
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
        return $this->getName() . ' (' . $this->getWidth() . 'x' . $this->getHeight() . ')';
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DeviceDisplay
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return DeviceDisplay
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return DeviceDisplay
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Add gameBuild
     *
     * @param GameBuild $gameBuild
     *
     * @return DeviceDisplay
     */
    public function addGameBuild(GameBuild $gameBuild)
    {
        $this->game_builds[] = $gameBuild;

        return $this;
    }

    /**
     * Remove gameBuild
     *
     * @param GameBuild $gameBuild
     */
    public function removeGameBuild(GameBuild $gameBuild)
    {
        $this->game_builds->removeElement($gameBuild);
    }

    /**
     * Get gameBuilds
     *
     * @return Collection
     */
    public function getGameBuilds()
    {
        return $this->game_builds;
    }
}
