<?php
namespace App\Domain\Entity;
use App\Domain\Entity\Interfaces\HasUuid;

/**
 * Created by PhpStorm.
 * User: Maxim Nevstruev
 * Date: 13.02.2017
 * Time: 16:57
 */
class Placeholder implements HasUuid
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var \App\Domain\Entity\Languages
     */
    private $language;

    /** @var string */
    private $uuid = null;

    /**
     * Placeholder constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set name
     *
     * @param string $name
     *
     * @return Placeholder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set defaultValue
     *
     * @param string $defaultValue
     *
     * @return Placeholder
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Get defaultValue
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set language
     *
     * @param \App\Domain\Entity\Languages $language
     *
     * @return Placeholder
     */
    public function setLanguage(\App\Domain\Entity\Languages $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \App\Domain\Entity\Languages
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
