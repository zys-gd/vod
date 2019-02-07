<?php

namespace PriceBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * Class Tier
 */
class Tier implements HasUuid
{
    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $name;

    /**
     * External Billing Framework tier id
     *
     * @var integer
     */
    protected $bfTierId;

    /**
     * @var Collection
     */
    private $values;

    /**
     * Tier constructor
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
    public function __toString()
    {
        return $this->getName() ?? '';
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
     * Returns the value of the tier
     * @return Collection | null
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Sets values for the tier
     * @param Collection $values
     */
    public function setValues(Collection $values)
    {
        $this->values = $values;
    }

    /**
     * Ads a value for the tier
     * @param TierValue $value
     */
    public function addValue(TierValue $value)
    {
        $this->values->add($value);
    }

    /**
     * Returns the entity id
     * @return int | null
     */
    public function getBfTierId()
    {
        return $this->bfTierId;
    }

    /**
     * Sets the entity id
     * @param int $id
     */
    public function setBfTierId(int $id = null)
    {
        $this->bfTierId = $id;
    }

    /**
     * Returns the entity name
     * @return string | null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the entity name
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(){
        return array(
            "id" => $this->getUuid(),
            "name" => $this->getName()
        );
    }
}