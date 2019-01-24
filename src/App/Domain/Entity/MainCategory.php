<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Subcategory
 */
class MainCategory
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
     * @var ArrayCollection
     */
    private $subcategories;

    /**
     * @var integer
     */
    private $menuPriority;

    /**
     * Subcategory constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->subcategories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return MainCategory
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getMenuPriority(): int
    {
        return $this->menuPriority;
    }

    /**
     * @param int $menuPriority
     *
     * @return MainCategory
     */
    public function setMenuPriority(int $menuPriority): self
    {
        $this->menuPriority = $menuPriority;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }
}