<?php

namespace SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SMSTextHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        $definition = $container->findDefinition('SubscriptionBundle\Subscription\Notification\SMSText\SMSTextProvider');

        $taggedServices = $container->findTaggedServiceIds('subscription.sms_text_handler');

        foreach ($taggedServices as $id => $tags) {

            foreach ($tags as $tag) {
                $definition->addMethodCall('addSMSTextHandler', [new Reference($id)]);
            }
        }
    }
}
