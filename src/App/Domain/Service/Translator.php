<?php

namespace App\Domain\Service;

use App\Domain\Service\Translator\TranslationProvider;
use App\Domain\Service\Translator\TranslatorShortcodeReplacer;
use SubscriptionBundle\Entity\SubscriptionPack;

class Translator
{
    /** @var TranslationProvider  */
    private $translationProvider;
    /** @var TranslatorShortcodeReplacer  */
    private $translatorShortcodeReplacer;

    /**
     * TranslatorService constructor.
     *
     * @param TranslationProvider $translatorProvider
     */
    public function __construct(TranslationProvider $translatorProvider, TranslatorShortcodeReplacer $translatorShortcodeReplacer)
    {
        $this->translationProvider = $translatorProvider;
        $this->translatorShortcodeReplacer = $translatorShortcodeReplacer;
    }

    /**
     * @param string $translationKey
     * @param null   $carrierId
     * @param string $languageCode
     *
     * @return string|null
     */
    public function translate(string $translationKey, $carrierId = null, string $languageCode = 'en'): ?string
    {
        $translate = $this->translationProvider->getTranslation($translationKey, $carrierId, $languageCode);

        return $translate;
    }

    // public function replaceShortcode()
    // {
    //     return $this->translatorShortcodeReplacer->do(/*$offer_placeholder, $oSubPack, $flattened*/);
    // }
    /**
     * @param $aTransformOptions
     * @return array
     */
    private function flattenTransformOptions($aTransformOptions): array
    {
        $flattened = [];
        foreach ($aTransformOptions as $option) {
            if (!is_array($option)) {
                $option = [$option];
            }
            $flattened = array_merge($flattened, $option);
        }
        return $flattened;
    }
}
