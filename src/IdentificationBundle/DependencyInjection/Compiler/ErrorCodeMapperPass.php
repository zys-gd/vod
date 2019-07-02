<?php

namespace IdentificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ErrorCodeMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers\ErrorCodeMapperProvider');

        $taggedServices = $container->findTaggedServiceIds('identification.error_code_mapper');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
