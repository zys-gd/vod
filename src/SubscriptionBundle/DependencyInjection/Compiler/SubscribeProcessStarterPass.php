<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SubscribeProcessStarterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        $definition = $container->findDefinition('SubscriptionBundle\Subscription\Subscribe\ProcessStarter\SubscribeProcessStarterProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.subscribe_process_starter');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
