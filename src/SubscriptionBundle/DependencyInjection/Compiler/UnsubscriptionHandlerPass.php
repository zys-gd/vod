<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UnsubscriptionHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.unsubscription_handler');

        foreach ($taggedServices as $id => $tags) {

            foreach ($tags as $tag) {
                $definition->addMethodCall('addHandler', [new Reference($id)]);
            }
        }
    }
}
