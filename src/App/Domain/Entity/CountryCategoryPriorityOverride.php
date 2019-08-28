<?php

namespace App\Domain\Entity;

use CommonDataBundle\Entity\Country;
use CommonDataBundle\Entity\Interfaces\HasUuid;

/**
 * Class CountryCategoryPriorityOverride
 */
class CountryCategoryPriorityOverride implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $menuPriority;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var MainCategory
     */
    private $mainCategory;

    /**
     * CountryCategoryPriorityOverride constructor
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
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return CountryCategoryPriorityOverride
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMenuPriority(): ?int
    {
        return $this->menuPriority;
    }

    /**
     * @param int $menuPriority
     *
     * @return CountryCategoryPriorityOverride
     */
    public function setMenuPriority(int $menuPriority): self
    {
        $this->menuPriority = $menuPriority;

        return $this;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     *
     * @return CountryCategoryPriorityOverride
     */
    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return MainCategory|null
     */
    public function getMainCategory(): ?MainCategory
    {
        return $this->mainCategory;
    }

    /**
     * @param MainCategory $mainCategory
     *
     * @return CountryCategoryPriorityOverride
     */
    public function setMainCategory(MainCategory $mainCategory): self
    {
        $this->mainCategory = $mainCategory;

        return $this;
    }
}