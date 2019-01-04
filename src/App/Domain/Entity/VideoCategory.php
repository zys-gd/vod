<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\HasUuid;


/**
 * VideoCategory
 */
class VideoCategory implements HasUuid
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $alias;

    private $uuid;

    /**
     * @var integer
     */
    private $menuPriority;

    /**
     * VideoCategory constructor.
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return VideoCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return VideoCategory
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
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

    public function getMenuPriority(): int
    {
        return $this->menuPriority;
    }

    public function setMenuPriority(int $menuPriority): VideoCategory
    {
        $this->menuPriority = $menuPriority;

        return $this;
    }
}

