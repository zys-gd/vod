<?php

namespace App\Tests\App\IdentificationBundle\Controller;


use GuzzleHttp\ClientInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Core\AbstractFunctionalTest;
use GuzzleHttp\Client;

class FakeIdentificationControllerTest extends AbstractFunctionalTest
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ClientInterface|MockInterface
     */
    private $httpClient;

    public function testIdentificationAction()
    {
        $client = $this->makeClient();
        $client->request('GET', 'identify');
    }

    protected function initializeServices(ContainerInterface $container)
    {
        $this->httpClient                   = \Mockery::spy(Client::class);
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        // TODO: Implement getFixturesListLoadedForEachTest() method.
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        // TODO: Implement configureWebClientClientContainer() method.
    }
}