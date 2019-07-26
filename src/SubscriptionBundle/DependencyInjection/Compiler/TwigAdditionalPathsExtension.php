<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.07.19
 * Time: 13:18
 */

namespace SubscriptionBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigAdditionalPathsExtension implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $twigFilesystemLoaderDefinition = $container->getDefinition('twig.loader.filesystem');

        $path = realpath(__DIR__ . '/../../Admin/Resources/templates');

        $twigFilesystemLoaderDefinition->addMethodCall('addPath', [
            $path, 'SubscriptionAdmin'
        ]);


    }
}