<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 17:14
 */

namespace IdentificationBundle\Tests\Functional;


use DataFixtures\LoadCarriersData;
use ExtrasBundle\Tests\Core\AbstractFunctionalTest;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use Mockery;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AutoIdentTest extends AbstractFunctionalTest
{

    /**
     * @var Mockery\MockInterface|IdentProcess
     */
    private $identProcess;

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }


    public function testWhoopsWhenIspDetectionFailed()
    {
        $client = $this->makeClient();

        $client->request('get', '/');

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('whoops', $location, 'redirect is missing');
        $this->assertArrayHasKey('storage[is_wifi_flow]', $this->session->all(), 'wifi flow are not set');
    }

    public function testNoRedirectForLP()
    {
        $client = $this->makeClient();

        $client->request('get', '/lp');
        $this->assertFalse($client->getResponse()->isRedirect(), 'redirect is triggered');
        $this->assertArrayHasKey('storage[is_wifi_flow]', $this->session->all(), 'wifi flow are not set');
    }

    public function testIsRedirectTriggeredForPixelIdent()
    {
        $client = $this->makeClient();

        $this->identProcess
            ->shouldReceive('doIdent')
            ->andReturn(new ProcessResult(null, 'pixel'));

        $client->request('get', '/', [], [], ['REMOTE_ADDR' => '119.160.116.250']);

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('identification/pixel/show-page', $location, 'redirect is missing');
    }

    public function testIsRedirectTriggeredForRedirectIdent()
    {
        $client = $this->makeClient();

        $this->identProcess
            ->shouldReceive('doIdent')
            ->andReturn(new ProcessResult(null, 'redirect', null, 'test-redirect'));

        $client->request('get', '/', [], [], ['REMOTE_ADDR' => '119.160.116.250']);

        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('test-redirect', $location, 'redirect is missing');
    }

    protected function initializeServices(ContainerInterface $container)
    {
        $this->identProcess = Mockery::spy(IdentProcess::class);
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
    }
}