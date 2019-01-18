<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * CategoryGameCountryLink
 */
class CategoryGameCountryLink implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $position;


    /**
     * @var Game
     */
    private $game;

    /**
     * @var CategoryCountryOverride
     */
    private $categoryOverride;

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return CategoryGameCountryLink
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set game
     *
     * @param Game $game
     *
     * @return CategoryGameCountryLink
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
     * Set categoryOverride
     *
     * @param CategoryCountryOverride $categoryOverride
     *
     * @return CategoryGameCountryLink
     */
    public function setCategoryOverride(CategoryCountryOverride $categoryOverride = null)
    {
        $this->categoryOverride = $categoryOverride;

        return $this;
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
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * Get categoryOverride
     *
     * @return CategoryCountryOverride
     */
    public function getCategoryOverride()
    {
        return $this->categoryOverride;
    }
}
