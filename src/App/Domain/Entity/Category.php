<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Category
 */
class Category
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
     * @var Category
     */
    private $parent;

    /**
     * @var ArrayCollection
     */
    private $childCategories;

    /**
     * @var integer
     */
    private $menuPriority;

    /**
     * Category constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->childCategories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Category
     */
    public function setTitle(string $title): Category
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
     * @param string $alias
     *
     * @return Category
     */
    public function setAlias(string $alias): Category
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias(): string
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
     * @return Category
     */
    public function setMenuPriority(int $menuPriority): Category
    {
        $this->menuPriority = $menuPriority;

        return $this;
    }

    /**
     * @param Category $category
     *
     * @return Category
     */
    public function setParent(Category $category): Category
    {
        $this->parent = $category;

        return $this;
    }

    /**
     * @return Category | null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @return Collection
     */
    public function getChildCategories(): Collection
    {
        return $this->childCategories;
    }
}