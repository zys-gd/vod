<?php

namespace App\Domain\Entity;

class Translation
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

    /** @var Carrier */
    private $carrier;

    /**
     * Translation constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Carrier
     */
    public function getCarrier(): Carrier
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
     * @return Languages
     */
    public function getLanguage(): Languages
    {
        return $this->language;
    }

    /**
     * @param Languages $language
     */
    public function setLanguage(Languages $language): void
    {
        $this->language = $language;
    }

    /** @var Languages */
    private $language;

    /**
     * @return string
     */
    public function getKey(): string
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
     * @return string
     */
    public function getTranslation(): string
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

}
