<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SubscriptionVoterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        $definition = $container->findDefinition('SubscriptionBundle\Subscription\Subscribe\Voter\BatchSubscriptionVoter');

        $taggedServices = $container->findTaggedServiceIds('subscription.voter');

        foreach ($taggedServices as $id => $tags) {

            foreach ($tags as $tag) {
                $definition->addMethodCall('addVoter', [new Reference($id)]);
            }
        }
    }
}
