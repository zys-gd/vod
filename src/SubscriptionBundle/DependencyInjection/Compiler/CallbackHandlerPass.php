<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CallbackHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.callback_carrier_handler');

        foreach ($taggedServices as $id => $tags) {

            foreach ($tags as $tag) {
                $type = $tag['type'];

                $definition->addMethodCall('addHandler', [new Reference($id), $type]);
            }
        }
    }
}
