<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 12:34
 */

namespace IdentificationBundle\Tests\Identification\Controller;


use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommonFlowTest extends AbstractFunctionalTest
{

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

    public function waitForCallbackIsRedirectedCorre()
    {

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