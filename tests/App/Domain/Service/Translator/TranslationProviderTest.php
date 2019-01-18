<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 18-01-19
 * Time: 12:54
 */

namespace App\Tests\App\Domain\Service\Translator;

use App\Domain\Entity\Language;
use App\Domain\Entity\Translation;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\LanguageRepository;
use App\Domain\Repository\TranslationRepository;
use App\Domain\Service\Translator\TranslationProvider;
use ExtrasBundle\Cache\ICacheService;
use PHPUnit\Framework\TestCase;
use Mockery;

class TranslationProviderTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var  TranslationRepository|\Mockery\MockInterface */
    private $translationRepository;
    /** @var  CarrierRepository|\Mockery\MockInterface */
    private $carrierRepository;
    /** @var  ICacheService|\Mockery\MockInterface */
    private $cache;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface */
    private $session;
    /** @var  LanguageRepository|\Mockery\MockInterface */
    private $languagesRepository;
    /** @var TranslationProvider|\Mockery\MockInterface */
    private $translatorProvider;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testGetTranslationWithoutCache()
    {
        $oLanguage = Mockery::spy(Language::class);
        $oLanguage->shouldReceive('getUuid')->andReturn('5179f17c-ebd4-11e8-95c4-02bb250f0f22');
        $this->languagesRepository->shouldReceive('findOneBy')->andReturn($oLanguage);

        $oTranslation = Mockery::spy(Translation::class);
        $oTranslation->allows([
            'getKey' => 'terms.block_2.text.1',
            'getTranslation' => 'This service is only available for users in [Country]. You must have permission from the carrier to use this service.',
            'getCarrier' => null,
            'getLanguage' => $oLanguage
        ]);
        $this->translationRepository->shouldReceive('findOneBy')->andReturn($oTranslation);
        $this->translationRepository->shouldReceive('findBy')->andReturn([$oTranslation]);

        $result = $this->translatorProvider->getTranslation('terms.block_2.text.1', null, 'en');

        $this->assertEquals('This service is only available for users in [Country]. You must have permission from the carrier to use this service.', $result);
    }

    protected function setUp()
    {
        $this->translationRepository = Mockery::spy(TranslationRepository::class);
        $this->carrierRepository = Mockery::spy(CarrierRepository::class);
        $this->cache = Mockery::spy(ICacheService::class);
        $this->languagesRepository = Mockery::spy(LanguageRepository::class);

        $this->translatorProvider = new TranslationProvider(
            $this->translationRepository,
            $this->carrierRepository,
            $this->languagesRepository,
            $this->cache
        );
    }
}
