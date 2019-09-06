<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 12:07
 */

namespace IdentificationBundle\Tests\Identification\Controller;


use CountryCarrierDetectionBundle\Service\IpService;
use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use Mockery;
use Redis;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IdentifyByUrlTest extends AbstractFunctionalTest
{
    private $ipService;
    private $redisConnectionProvider;

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

    public function testRedirectIsPerformedForV1Urls()
    {
        $client = $this->makeClient();

        $this->ipService->allows(['getIp' => '127.0.0.1']);

        $client->request('get', '/identification/identify', ['urlId' => 123]);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/identification/identify-by-url?urlId=123'),
            'redirect is not properly performed'
        );
    }

    public function testHomepageRedirectAfterIdentifiedByUrl()
    {
        $client = $this->makeClient();

        $this->ipService->allows(['getIp' => '127.0.0.1']);

        $client->request('get', '/identification/identify-by-url', ['urlId' => 123, 'f' => 1]);

        $this->assertTrue(
            $client->getResponse()->isRedirect(),
            'redirect is not properly performed'
        );
    }

    protected function initializeServices(ContainerInterface $container)
    {
        $this->ipService               = Mockery::spy(IpService::class);
        $this->redisConnectionProvider = Mockery::spy(\ExtrasBundle\Cache\Redis\RedisConnectionProvider::class);

        $this->redisConnectionProvider->allows(['create' => Mockery::mock(Redis::class)]);
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        $container->set('app.cache.redis_connection_provider', $this->redisConnectionProvider);
        $container->set('CountryCarrierDetectionBundle\Service\IpService', $this->ipService);
    }
}