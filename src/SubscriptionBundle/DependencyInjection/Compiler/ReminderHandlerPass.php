<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ReminderHandlerPass
 */
class ReminderHandlerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('SubscriptionBundle\Reminder\ReminderHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.remind_handler');

        foreach ($taggedServices as $id => $tags) {

            foreach ($tags as $tag) {
                $definition->addMethodCall('addHandler', [new Reference($id)]);
            }
        }
    }
}