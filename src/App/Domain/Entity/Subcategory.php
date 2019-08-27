<?php

namespace App\Domain\Entity;

use CommonDataBundle\Entity\Interfaces\HasUuid;
use JsonSerializable;

/**
 * Subcategory
 */
class Subcategory implements JsonSerializable, HasUuid
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
     * @var MainCategory
     */
    private $parent;

    /**
     * Subcategory constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Subcategory
     */
    public function setTitle(string $title): Subcategory
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
     * @return Subcategory
     */
    public function setAlias(string $alias): Subcategory
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
     * @param MainCategory $category
     *
     * @return Subcategory
     */
    public function setParent(MainCategory $category): Subcategory
    {
        $this->parent = $category;

        return $this;
    }

    /**
     * @return MainCategory
     */
    public function getParent(): ?MainCategory
    {
        return $this->parent;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getUuid(),
            'title' => $this->getTitle(),
        ];
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }
}