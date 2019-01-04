<?php

namespace App\Domain\Entity;

class Translations
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
    private $translate;

    /** @var Carrier */
    private $carrier;

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
    public function getTranslate(): string
    {
        return $this->translate;
    }

    /**
     * @param string $translate
     */
    public function setTranslate(string $translate): void
    {
        $this->translate = $translate;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

}
