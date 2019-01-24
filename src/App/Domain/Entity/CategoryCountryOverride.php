<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CategoryCountryOverride
 */
class CategoryCountryOverride implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * This array is only used by sonataAdmin to map the games as array of IDs.
     *
     * @var array
     */
    public $games = [];

    /**
     * @var Collection
     */
    private $gamesLinks;

    /**
     * String representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'Country Override';
    }

    /**
     * @var Subcategory
     */
    private $category;

    /**
     * @var Collection
     */
    private $countries;

    /**
     * Constructor
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->countries = new ArrayCollection();
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
     * Set category
     *
     * @param Subcategory $category
     *
     * @return CategoryCountryOverride
     */
    public function setCategory(Subcategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Subcategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add country
     *
     * @param Country $country
     *
     * @return CategoryCountryOverride
     */
    public function addCountry(Country $country)
    {
        $this->countries[] = $country;

        return $this;
    }

    /**
     * Remove country
     *
     * @param Country $country
     */
    public function removeCountry(Country $country)
    {
        $this->countries->removeElement($country);
    }

    /**
     * Get countries
     *
     * @return Collection
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Add a game link
     *
     * @param CategoryGameCountryLink $gamesLink
     *
     * @return CategoryCountryOverride
     */
    public function addGamesLink(CategoryGameCountryLink $gamesLink)
    {
        $this->gamesLinks[] = $gamesLink;

        return $this;
    }

    /**
     * Remove a game link
     *
     * @param CategoryGameCountryLink $gamesLink
     */
    public function removeGamesLink(CategoryGameCountryLink $gamesLink)
    {
        $this->gamesLinks->removeElement($gamesLink);
    }

    /**
     * Get all games' links
     *
     * @return Collection
     */
    public function getGamesLinks()
    {
        return $this->gamesLinks;
    }
}
