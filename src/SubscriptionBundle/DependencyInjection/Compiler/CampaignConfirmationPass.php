<?php


namespace SubscriptionBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CampaignConfirmationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('SubscriptionBundle\Service\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.campaign_confirmation_handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}