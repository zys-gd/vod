<?php


namespace App\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OneClickFlowHandlerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('App\Domain\Service\OneClickFlow\OneClickFlowCarriersProvider');

        $taggedServices = $container->findTaggedServiceIds('app.one_click_flow_handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}