<?php

namespace App\Domain\Service\Translator;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Translation;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\LanguageRepository;
use App\Domain\Repository\TranslationRepository;
use ExtrasBundle\Cache\ICacheService;

class Translator
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
        $this->carrierRepository     = $carrierRepository;
        $this->cache                 = $cache;
        $this->languageRepository    = $languageRepository;
    }

    /**
     * @param string $translationKey
     * @param        $billingCarrierId
     * @param string $languageCode
     *
     * @return string|null
     */
    public function translate(string $translationKey, $billingCarrierId, string $languageCode): ?string
    {
        $cacheKey = $this->generateCacheKey($billingCarrierId, $languageCode);
        // if cache exist
        if ($this->isCacheExist($cacheKey)) {
            $this->extractCache($cacheKey);
            if (!isset($this->texts[$translationKey])) {
                $this->doTranslate($translationKey, $billingCarrierId, $languageCode)
                    ->pushTexts2Cache($cacheKey);
            }
        } else {
            $this->initializeDefaultTexts()
                ->initializeCarrierTexts($billingCarrierId, $languageCode)
                ->pushTexts2Cache($cacheKey);
        }

        return $this->texts[$translationKey] ?? null;
    }

    /**
     * @param string $translationKey
     * @param        $billingCarrierId
     * @param string $languageCode
     *
     * @return $this
     */
    private function doTranslate(string $translationKey, $billingCarrierId, string $languageCode)
    {
        $translation = $this->receiveFromDb($translationKey, $billingCarrierId, $languageCode);
        if (!is_null($translation)) {
            $this->texts[$translationKey] = $translation->getTranslation();
        }
        return $this;
    }

    /**
     * @param $translationKey
     * @param $billingCarrierId
     * @param $languageCode
     *
     * @return Translation|null
     */
    private function receiveFromDb($translationKey, $billingCarrierId, $languageCode): ?Translation
    {
        /** @var Carrier $oCarrier */
        $oCarrier = $this->carrierRepository->findOneBy(['billingCarrierId' => $billingCarrierId]);
        $oLanguage = $this->languageRepository->findOneBy(['code' => $languageCode]);
        /** @var Translation $translation */
        $translation = $this->translationRepository->findOneBy([
            'language' => $oLanguage,
            'carrier'  => $oCarrier,
            'key'      => $translationKey
        ]);
        return $translation;
    }

    /**
     * @return $this
     */
    private function initializeDefaultTexts()
    {
        $locale = self::DEFAULT_LOCALE;
        $oLanguage = $this->languageRepository->findOneBy(['code' => $locale]);
        /** @var Translation[] $translations */
        $translations = $this->translationRepository->findBy([
            'language' => $oLanguage,
            'carrier'  => null
        ]);
        foreach ($translations ?? [] as $translation) {
            $this->texts[$translation->getKey()] = $translation->getTranslation();
        }

        return $this;
    }

    /**
     * @param        $billingCarrierId
     * @param string $languageCode
     *
     * @return $this
     */
    private function initializeCarrierTexts(int $billingCarrierId, string $languageCode)
    {
        /** @var Carrier $oCarrier */
        $oCarrier = $this->carrierRepository->findOneBy([
            'billingCarrierId' => $billingCarrierId
        ]);

        $selectedCode = ($oCarrier != null)
            ? $oCarrier->getDefaultLanguage()->getCode()
            : $languageCode;


        /** @var Translation[] $translations */
        $translations = $this->translationRepository->findTranslationForCarrier($oCarrier, $selectedCode);

        foreach ($translations ?? [] as $translation) {
            $this->texts[$translation['key']] = $translation['translation'];
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