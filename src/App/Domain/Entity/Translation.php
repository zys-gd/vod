<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

class Translation implements HasUuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $translation;

    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * @var Language
     */
    private $language;

    /**
     * Translation constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Carrier|null
     */
    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    /**
     * @param Carrier $carrier
     */
    public function setCarrier(Carrier $carrier): void
    {
        $this->carrier = $carrier;
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string|null
     */
    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    /**
     * @param string $translation
     */
    public function setTranslation(string $translation): void
    {
        $this->translation = $translation;
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
}
