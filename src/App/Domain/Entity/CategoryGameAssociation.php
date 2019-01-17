<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * Class CategoryGameAssociation
 * @package App\Domain\Entity
 */
class CategoryGameAssociation implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private $updateFlag = false;

    /**
     * CategoryGameAssociation constructor.
     * @throws \Exception
     */
    public function __construct(string $uuid)
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
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
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
     * Set game
     *
     * @param Game $game
     *
     * @return CategoryGameAssociation
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get categories
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return CategoryGameAssociation
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get 'sortable' (i.e. priority) position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set 'sortable' (i.e. priority) position
     *
     * @param integer $position
     *
     * @return CategoryGameAssociation
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of updatedFlag
     * This is used for marking the object as 'modified'.
     *
     * @return bool
     */
    public function getUpdatedFlag()
    {
        return $this->updateFlag;
    }

    /**
     * Set the updated flag to a bool value.
     * This is used for marking the object as 'modified'.
     *
     * @param bool $flag
     * @return $this
     */
    public function setUpdatedFlag($flag)
    {
        $this->updateFlag = $flag;

        return $this;
    }
}
