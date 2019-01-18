<?php

namespace App\Domain\Service\Translator;

use App\Domain\Entity\Translation;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\LanguageRepository;
use App\Domain\Repository\TranslationRepository;
use ExtrasBundle\Cache\ICacheService;

class TranslationProvider
{
    const DEFAULT_LOCALE = 'en';

    /** @var TranslationRepository */
    protected $translationRepository;

    /** @var ICacheService */
    protected $cache;
    /** @var LanguageRepository */
    private $languageRepository;

    private $texts = [];

    public function __construct(
        TranslationRepository $translationRepository,
        CarrierRepository $carrierRepository,
        LanguageRepository $languageRepository,
        ICacheService $cache
    )
    {
        $this->translationRepository = $translationRepository;
        $this->carrierRepository = $carrierRepository;
        $this->cache = $cache;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param string $translationKey
     * @param        $carrierId
     * @param string $languageCode
     *
     * @return string|null
     */
    public function getTranslation(string $translationKey, $carrierId, string $languageCode): ?string
    {
        $cacheKey = $this->generateCacheKey($carrierId, $languageCode);
        // if cache exist
        if ($this->isCacheExist($cacheKey)) {
            $this->extractCache($cacheKey);
            if (!isset($this->texts[$translationKey])) {
                $this->doTranslate($translationKey, $carrierId, $languageCode)
                    ->pushTexts2Cache($cacheKey);
            }
        }
        else {
            $this->initializeDefaultTexts()
                ->doTranslate($translationKey, $carrierId, $languageCode)
                ->pushTexts2Cache($cacheKey);
        }
        return $this->texts[$translationKey];
    }

    /**
     * @param string $translationKey
     * @param        $carrierId
     * @param string $languageCode
     *
     * @return $this
     */
    private function doTranslate(string $translationKey, $carrierId, string $languageCode)
    {
        $translation = $this->receiveFromDb($translationKey, $carrierId, $languageCode);
        if(!is_null($translation)) {
            $this->texts[$translationKey] = $translation->getTranslation();
        }
        return $this;
    }

    /**
     * @param $translationKey
     * @param $carrierId
     * @param $languageCode
     *
     * @return Translation|null
     */
    private function receiveFromDb($translationKey, $carrierId, $languageCode): ?Translation
    {
        $oLanguage = $this->languageRepository->findOneBy(['code' => $languageCode]);
        /** @var Translation $translation */
        $translation = $this->translationRepository->findOneBy([
            'language' => $oLanguage,
            'carrier' => $carrierId,
            'key' => $translationKey
        ]);
        return $translation;
    }

    /**
     * @return $this
     */
    private function initializeDefaultTexts()
    {
        $oLanguage   = $this->languageRepository->findOneBy(['code' => self::DEFAULT_LOCALE]);
        /** @var Translation[] $translations */
        $translations = $this->translationRepository->findBy([
            'language' => $oLanguage
        ]);
        foreach ($translations ?? [] as $translation) {
            $this->texts[$translation->getKey()] = $translation->getTranslation();
        }
        return $this;
    }

    /**
     * @param string $cacheKey
     */
    private function extractCache(string $cacheKey)
    {
        $this->texts = $this->cache->getValue($cacheKey);
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed
     */
    private function isCacheExist(string $cacheKey)
    {
        return $this->cache->hasCache($cacheKey);
    }

    private function generateCacheKey($carrierId, string $languageCode)
    {
        return base64_encode("translations_{$languageCode}_{$carrierId}");
    }

    private function pushTexts2Cache($cacheKey)
    {
        $this->cache->saveCache($cacheKey, $this->texts, 86400);
        return $this;
    }
}