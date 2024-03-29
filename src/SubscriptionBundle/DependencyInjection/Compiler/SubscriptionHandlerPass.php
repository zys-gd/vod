<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SubscriptionHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.subscription_handler');

        foreach ($taggedServices as $id => $tags) {

            foreach ($tags as $tag) {
                $definition->addMethodCall('addHandler', [new Reference($id)]);
            }
        }
    }
}
