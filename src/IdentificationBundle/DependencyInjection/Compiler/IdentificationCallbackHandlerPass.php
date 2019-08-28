<?php

namespace IdentificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class IdentificationCallbackHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('IdentificationBundle\Callback\Handler\IdentCallbackHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('identification.identification_callback_handler');

        foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
