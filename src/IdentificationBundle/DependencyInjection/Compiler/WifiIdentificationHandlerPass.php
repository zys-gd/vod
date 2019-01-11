<?php

namespace IdentificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WifiIdentificationHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        $definition = $container->findDefinition('IdentificationBundle\Service\Action\WifiIdentification\Handler\WifiIdentificationHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('identification.wifi_identification_handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
