<?php

namespace App\Domain\Service\Translator;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Translation;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\TranslationRepository;
use CommonDataBundle\Entity\Language;
use CommonDataBundle\Repository\LanguageRepository;
use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;
use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Identification\Service\TranslatorInterface;

/**
 * Class Translator
 */
class Translator implements TranslatorInterface
{
    const DEFAULT_LOCALE = 'en';

    /**
     * @var TranslationRepository
     */
    protected $translationRepository;

    /**
     * @var ICacheService
     */
    protected $cache;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var array
     */
    private $texts = [];

    /**
     * @var ArrayCacheService
     */
    private $arrayCacheService;

    /**
     * Translator constructor
     *
     * @param TranslationRepository $translationRepository
     * @param CarrierRepository     $carrierRepository
     * @param LanguageRepository    $languageRepository
     * @param ICacheService         $cache
     */
    public function __construct(
        TranslationRepository $translationRepository,
        CarrierRepository $carrierRepository,
        LanguageRepository $languageRepository,
        ICacheService $cache,
        ArrayCacheService $arrayCacheService
    )
    {
        $this->translationRepository = $translationRepository;
        $this->cache                 = $cache;
        $this->languageRepository    = $languageRepository;
        $this->carrierRepository     = $carrierRepository;
        $this->arrayCacheService     = $arrayCacheService;
    }

    /**
     * @param string $translationKey
     * @param        $billingCarrierId
     * @param string $languageCode
     *
     * @return string|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function translate(string $translationKey, $billingCarrierId, string $languageCode): ?string
    {
        $cacheKey = $this->generateCacheKey($billingCarrierId, $languageCode);

        if ($this->texts) {
            if (!array_key_exists($translationKey, $this->texts)) {
                $this
                    ->doTranslate($translationKey, $billingCarrierId, $languageCode)
                    ->pushTexts2Cache($cacheKey);
            }
        } else {
            if ($this->arrayCacheService->hasCache($cacheKey)) {
                $this->texts = $this->arrayCacheService->getValue($cacheKey);
            } elseif ($this->cache->hasCache($cacheKey)) {
                $this->texts = $this->cache->getValue($cacheKey);
                $this->arrayCacheService->saveCache($cacheKey, $this->texts, 86400);
            } else {
                $this
                    ->retrieveFromDB($billingCarrierId, $languageCode)
                    ->pushTexts2Cache($cacheKey);
            }
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
        } else {
            $this->texts[$translationKey] = $translationKey;
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
        $oCarrier  = $this->carrierRepository->findOneBy(['billingCarrierId' => $billingCarrierId]);
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
     * @param        $billingCarrierId
     * @param string $languageCode
     *
     * @return $this
     */
    private function retrieveFromDB($billingCarrierId, string $languageCode)
    {
        /** @var Language $defaultLanguage */
        $defaultLanguage = $this->languageRepository->findOneBy(['code' => self::DEFAULT_LOCALE]);

        /** @var Translation[] $defaultTexts */
        $defaultTexts = $this->translationRepository->findBy([
            'language' => $defaultLanguage,
            'carrier'  => null
        ]);

        /** @var Language $defaultLanguage */
        $userLanguage = $defaultLanguage;

        /** @var Translation[] $defaultTextsForCurrentLang */
        $defaultTextsForCurrentLang = [];
        if ($languageCode != self::DEFAULT_LOCALE) {
            $userLanguage               = $this->languageRepository->findOneBy(['code' => $languageCode]);
            $defaultTextsForCurrentLang = $this->translationRepository->findBy([
                    'language' => $userLanguage,
                    'carrier'  => null
                ]) ?? [];
        }

        try {
            /** @var Carrier $oCarrier */
            $oCarrier = $this->carrierRepository->findOneBy(['billingCarrierId' => $billingCarrierId]);
            /** @var Language $currentLanguage */
            $currentLanguage = $oCarrier->getDefaultLanguage() ?? $userLanguage;

            $defaultCarrierTexts = $this->translationRepository->findBy([
                'carrier'  => $oCarrier,
                'language' => $defaultLanguage
            ]);

            $currentCarrierTexts = $this->translationRepository->findBy([
                'carrier'  => $oCarrier,
                'language' => $currentLanguage
            ]);

            $defaultTextsForDefaultCarrierLang = $this->translationRepository->findBy([
                'language' => $currentLanguage
            ]);

            $translations = array_merge(
                $defaultTexts,
                $defaultTextsForCurrentLang,
                $defaultCarrierTexts,
                $currentCarrierTexts,
                $defaultTextsForDefaultCarrierLang
            );
        } catch (\Throwable $e) {
            $translations = array_merge($defaultTexts, $defaultTextsForCurrentLang);
        }

        /** @var Translation[] $translations */
        foreach ($translations as $translation) {
            $this->texts[$translation->getKey()] = $translation->getTranslation();
        }

        return $this;
    }


    private function generateCacheKey($carrierId, string $languageCode): string
    {
        return "translations_{$languageCode}_{$carrierId}";
    }

    private function pushTexts2Cache($cacheKey)
    {
        $this->arrayCacheService->saveCache($cacheKey, $this->texts, 86400);
        $this->cache->saveCache($cacheKey, $this->texts, 86400);
        return $this;
    }
}