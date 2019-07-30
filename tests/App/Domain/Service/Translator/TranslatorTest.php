<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 18-01-19
 * Time: 12:54
 */

namespace App\Tests\App\Domain\Service\Translator;

use App\Domain\Entity\Carrier;
use CommonDataBundle\Entity\Language;
use App\Domain\Entity\Translation;
use App\Domain\Repository\CarrierRepository;
use CommonDataBundle\Repository\LanguageRepository;
use App\Domain\Repository\TranslationRepository;
use App\Domain\Service\Translator\Translator;
use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;
use ExtrasBundle\Utils\UuidGenerator;
use ExtrasBundle\Cache\ICacheService;
use PHPUnit\Framework\TestCase;
use Mockery;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class TranslatorTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var  TranslationRepository|\Mockery\MockInterface */
    private $translationRepository;
    /** @var  CarrierRepository|\Mockery\MockInterface */
    private $carrierRepository;
    /** @var  ICacheService|\Mockery\MockInterface */
    private $cache;
    /** @var  LanguageRepository|\Mockery\MockInterface */
    private $languagesRepository;
    /** @var Translator|\Mockery\MockInterface */
    private $translatorProvider;
    private $arrayCache;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testGetTranslationWithoutCache()
    {
        $this->cache->shouldReceive('hasCache')->andReturn(false);
        $this->arrayCache->shouldReceive('hasCache')->andReturn(false);

        $oLanguage = $this->mockLanguageEntity();
        $this->languagesRepository->shouldReceive('findOneBy')->once()->with(['code' => 'en'])->andReturn($oLanguage);

        $oTranslation = $this->mockTranslationEntity($oLanguage);

        $this->translationRepository->shouldReceive('findBy')->once()->with([
            'language' => $oLanguage,
            'carrier'  => null
        ])->andReturn([$oTranslation]);

        $result = $this->translatorProvider->translate('terms.block_2.text.1', null, 'en');

        // default text
        $this->assertEquals('This service is only available for users in [Country]. You must have permission from the carrier to use this service.', $result);

    }

    public function testGetTranslationWithCache()
    {

        $this->cache->shouldReceive('hasCache')->andReturn(true);

        $oLanguage    = $this->mockLanguageEntity();
        $oTranslation = $this->mockTranslationEntity($oLanguage);
        $aTexts       = [
            "{$oTranslation->getKey()}" => $oTranslation->getTranslation()
        ];
        $this->cache->shouldReceive('getValue')->andReturn($aTexts);

        $result = $this->translatorProvider->translate('terms.block_2.text.1', null, 'en');

        // default text
        $this->assertEquals('This service is only available for users in [Country]. You must have permission from the carrier to use this service.', $result);
    }

    /**
     * @param $oLanguage
     *
     * @return Translation|Mockery\MockInterface
     */
    private function mockTranslationEntity($oLanguage)
    {
        $oTranslation = Mockery::spy(Translation::class);
        $oTranslation->allows([
            'getKey'         => 'terms.block_2.text.1',
            'getTranslation' => 'This service is only available for users in [Country]. You must have permission from the carrier to use this service.',
            'getCarrier'     => null,
            'getLanguage'    => $oLanguage
        ]);
        return $oTranslation;
    }


    protected function setUp()
    {
        $this->translationRepository = Mockery::spy(TranslationRepository::class);
        $this->carrierRepository     = Mockery::spy(CarrierRepository::class);
        $this->cache                 = Mockery::spy(ICacheService::class);
        $this->languagesRepository   = Mockery::spy(LanguageRepository::class);
        $this->arrayCache            = Mockery::spy(ArrayCacheService::class);
        $this->translatorProvider    = new Translator(
            $this->translationRepository,
            $this->carrierRepository,
            $this->languagesRepository,
            $this->cache,
            $this->arrayCache

        );
    }

    /**
     * @return Language|Mockery\MockInterface
     */
    private function mockLanguageEntity()
    {
        $oLanguage = Mockery::spy(Language::class);
        $oLanguage->allows([
            'getUuid' => '5179f17c-ebd4-11e8-95c4-02bb250f0f22',
            'getCode' => 'en'
        ]);
        return $oLanguage;
    }
}
