<?php

namespace App\Domain\Entity;

use CommonDataBundle\Entity\Interfaces\HasUuid;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;

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
     * @var LanguageInterface
     */
    private $language;

    /**
     * Translation constructor.
     *
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
     * @return LanguageInterface|null
     */
    public function getLanguage(): ?LanguageInterface
    {
        return $this->language;
    }

    /**
     * @param LanguageInterface $language
     */
    public function setLanguage(LanguageInterface $language): void
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
