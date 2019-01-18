<?php

namespace App\Domain\Service\Translator;

use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\LanguageRepository;
use App\Domain\Repository\TranslationRepository;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class TranslationProvider
{
    const DEFAULT_LOCALE = 'en';

    /** @var TranslationRepository */
    protected $translationRepository;

    /** @var AdapterInterface */
    protected $cache;
    /** @var LanguageRepository */
    private $languageRepository;

    private $texts = [];

    public function __construct(
        TranslationRepository $translationRepository,
        CarrierRepository $carrierRepository,
        LanguageRepository $languageRepository,
        AdapterInterface $cache
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
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getTranslation(string $translationKey, $carrierId, string $languageCode)
    {
        $cacheKey = $this->generateCacheKey($carrierId, $languageCode);
        $cacheItem = $this->extractCache($cacheKey);
        // if cache exist
        if ($cacheItem->get()) {
            $this->extractTextsFromCache($cacheItem);
            if (!isset($this->texts[$translationKey])) {
                $this->doTranslate($translationKey, $carrierId, $languageCode)
                    ->pushTexts2Cache($cacheItem);
            }
        }
        else {
            $this->initializeDefaultTexts()
                ->doTranslate($translationKey, $carrierId, $languageCode)
                ->pushTexts2Cache($cacheItem);
        }
        return $this->texts[$translationKey];
    }

    /**
     * @param string $translationKey
     * @param        $carrierId
     * @param string $languageCode
     *
     * @return object|null
     */
    private function doTranslate(string $translationKey, $carrierId, string $languageCode)
    {
        $translation = $this->receiveFromDb($translationKey, $carrierId, $languageCode);
        if(!is_null($translation)) {
            $this->texts[$translationKey] = $translation;
        }
        return $this;
    }

    /**
     * @param $translationKey
     * @param $carrierId
     * @param $languageCode
     *
     * @return object|null
     */
    private function receiveFromDb($translationKey, $carrierId, $languageCode)
    {
        $oLanguage = $this->languageRepository->findOneBy(['code' => $languageCode]);
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
    // TODO: put to cache?
    private function initializeDefaultTexts()
    {
        $oLanguage   = $this->languageRepository->findOneBy(['code' => self::DEFAULT_LOCALE]);
        $translation = $this->translationRepository->findBy([
            'language' => $oLanguage
        ]);
        $this->texts = json_decode(json_encode($translation), true);
        return $this;
    }

    /**
     * @param string $cacheKey
     *
     * @return \Symfony\Component\Cache\CacheItem
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function extractCache(string $cacheKey)
    {
        return $this->cache->getItem($cacheKey);
    }

    /**
     * @param \Symfony\Component\Cache\CacheItem $cacheItem
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function extractTextsFromCache(\Symfony\Component\Cache\CacheItem $cacheItem)
    {
        $this->texts = $this->cache->getItem($cacheItem->getKey())->get();
    }

    private function generateCacheKey($carrierId, string $languageCode)
    {
        return base64_encode("translations_{$languageCode}_{$carrierId}");
    }

    private function pushTexts2Cache(\Symfony\Component\Cache\CacheItem $cacheItem)
    {
        $cacheItem->set($this->texts);
        $this->cache->save($cacheItem);
        return $this;
    }
}