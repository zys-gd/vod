<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/18/2019
 * Time: 12:35 PM
 */

namespace App\Tests\App\Functional;

use App\Domain\Service\Translator\TranslationProvider;
use DataFixtures\LoadTranslationsData;
use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use Mockery;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentControllerTest extends AbstractFunctionalTest
{
    /**
     * @var Mockery\MockInterface|IdentProcess
     */
    protected $translationProvider;

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

    /**
     * @throws \Exception
     */
    public function testFaqPage()
    {
        $client = $this->makeClient();
        $client->request('GET', '/faq');

        $this->assertContains('faq.q.1', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testTermsAndConditionsPage()
    {

        $client = $this->makeClient();
        $client->request('GET', '/terms-and-conditions');

        $this->assertContains('terms.block_1.title.1', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    protected function initializeServices(ContainerInterface $container)
    {
        $this->translationProvider = Mockery::spy(TranslationProvider::class);
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadTranslationsData::class
        ];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        $container->set('App\Domain\Service\Translator\TranslationProvider', $this->translationProvider);
    }
}