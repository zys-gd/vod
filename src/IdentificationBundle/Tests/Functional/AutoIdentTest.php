<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 17:14
 */

namespace IdentificationBundle\Tests\Functional;


use DataFixtures\LoadCarriersData;
use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Identification\Service\DeviceDataProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use Mockery;
use Redis;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AutoIdentTest extends AbstractFunctionalTest
{

    /**
     * @var Mockery\MockInterface|IdentProcess
     */
    private $identProcess;
    private $redisConnectionProvider;

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }


    public function testWhoopsWhenIspDetectionFailed()
    {
        $client = $this->makeClient();

        $client->request('get', '/', ['f' => 1]);

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('lp', $location, 'redirect is missing');
        $this->assertArrayHasKey('storage[is_wifi_flow]', $this->session->all(), 'wifi flow are not set');
    }

    public function testIsRedirectTriggeredForPixelIdent()
    {
        $client = $this->makeClient();

        $this->identProcess
            ->shouldReceive('doIdent')
            ->andReturn(new ProcessResult(null, 'pixel'));

        $client->request('get', '/', ['f' => 1], [], ['REMOTE_ADDR' => '119.160.116.250']);

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('identification/pixel/show-page', $location, 'redirect is missing');
    }

    public function testIsRedirectTriggeredForRedirectIdent()
    {
        $client = $this->makeClient();

        $this->identProcess
            ->shouldReceive('doIdent')
            ->andReturn(new ProcessResult(null, 'redirect', null, 'test-redirect'));

        $client->request('get', '/', ['f' => 1], [], ['REMOTE_ADDR' => '119.160.116.250']);

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('test-redirect', $location, 'redirect is missing');
    }

    public function testRedirectIsPerformedWhenNoCarrierSelected()
    {
        $client = $this->makeClient();

        $this->session->set('storage[is_wifi_flow]', true);
        $this->session->set(IdentificationDataStorage::IDENTIFICATION_DATA_KEY, ['carrier_id' => null]);

        $client->request('get', '/', ['f' => 1]);

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('lp', $location, 'redirect is missing');

    }


    public function testNoRedirectForLPWhenNoCarrierDetected()
    {
        $client = $this->makeClient();

        $client->request('get', '/lp', ['f' => 1]);

        $this->assertFalse($client->getResponse()->isRedirect(), 'redirect is triggered');
        $this->assertArrayHasKey('storage[is_wifi_flow]', $this->session->all(), 'wifi flow are not set');
    }


    public function testNoRedirectOnLPWhenWifiFlow()
    {
        $client = $this->makeClient();

        $this->session->set('storage[is_wifi_flow]', true);

        $client->request('get', '/lp', ['f' => 1]);

        $this->assertFalse($client->getResponse()->isRedirect(), 'redirect is triggered');

    }

    protected function initializeServices(ContainerInterface $container)
    {
        $this->identProcess            = Mockery::spy(IdentProcess::class);
        $this->redisConnectionProvider = Mockery::spy(\ExtrasBundle\Cache\Redis\RedisConnectionProvider::class);
        $this->redisConnectionProvider->allows(['create' => Mockery::mock(Redis::class)]);
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadCarriersData::class
        ];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        $container->set('IdentificationBundle\BillingFramework\Process\IdentProcess', $this->identProcess);
        $container->set('app.cache.redis_connection_provider', $this->redisConnectionProvider);
        $container->set('IdentificationBundle\Identification\Service\DeviceDataProvider', Mockery::mock(DeviceDataProvider::class)->shouldIgnoreMissing());
    }
}