<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/18/2019
 * Time: 12:35 PM
 */

namespace App\Tests\App\Functional;

use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentControllerTest extends AbstractFunctionalTest
{
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

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testTermsAndConditionsPage()
    {
        $client = $this->makeClient();

        $client->request('GET', '/terms-and-conditions');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    protected function initializeServices(ContainerInterface $container)
    {

    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {

    }
}