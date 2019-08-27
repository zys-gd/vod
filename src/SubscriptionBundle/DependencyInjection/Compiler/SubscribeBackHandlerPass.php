<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SubscribeBackHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('SubscriptionBundle\Service\Action\SubscribeBack\Handler\SubscribeBackHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.subscribeback_handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
