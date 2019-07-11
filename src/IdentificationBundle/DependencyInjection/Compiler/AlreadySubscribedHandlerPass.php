<?php

namespace IdentificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AlreadySubscribedHandlerPass
 */
class AlreadySubscribedHandlerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('IdentificationBundle\Identification\Handler\AlreadySubscribedHandlerProvider');
        $taggedServices = $container->findTaggedServiceIds('identification.already_subscribed_handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}