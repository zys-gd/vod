<?php

namespace App\Domain\Entity;

/**
 * VideoCategory
 */
class VideoCategory
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var integer
     */
    private $menuPriority;

    /**
     * VideoCategory constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
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

