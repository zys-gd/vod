<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 12:07
 */

namespace IdentificationBundle\Tests\Identification\Controller;


use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IdentifyByUrlTest extends AbstractFunctionalTest
{
    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

    public function testRedirectIsPerformedForV1Urls()
    {
        $client = $this->createClient();

        $client->request('get', '/identification/identify', ['urlId' => 123]);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/identification/identify-by-url?urlId=123'),
            'redirect is not properly performed'
        );
    }

    public function testHomepageRedirectAfterIdentifiedByUrl()
    {
        $client = $this->createClient();

        $client->request('get', '/identification/identify-by-url', ['urlId' => 123]);

        $this->assertTrue(
            $client->getResponse()->isRedirect(),
            'redirect is not properly performed'
        );
    }

    protected function initializeServices(ContainerInterface $container)
    {
        // TODO: Implement initializeServices() method.
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        // TODO: Implement configureWebClientClientContainer() method.
    }
}