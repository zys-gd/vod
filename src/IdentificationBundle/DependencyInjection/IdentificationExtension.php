<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:54
 */

namespace IdentificationBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class IdentificationExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('listeners.yml');
        $loader->load('services.yml');
        $loader->load('action-ident.yml');
        $loader->load('action-ident-callback.yml');
        $loader->load('action-wifi-ident.yml');
        $loader->load('billing-framework-integration.yml');
        $loader->load('repositories.yml');
        $loader->load('profiler.yml');
        $loader->load('twig.yml');
        $loader->load('user.yml');


        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/carriers'));

        foreach (glob(__DIR__ . '/../Resources/config/carriers/*.yml') as $file) {
            $loader->load(basename($file));
        }

        $service = $container->getDefinition('IdentificationBundle\Identification\Service\RouteProvider');
        $service->replaceArgument(1, $mergedConfig['wifi_flow_redirect_route']);
        $service->replaceArgument(2, $mergedConfig['homepage_route']);
        $service->replaceArgument(3, $mergedConfig['landing_route']);
        $service->replaceArgument(4, $mergedConfig['my_account_route']);
        $service->replaceArgument(5, $mergedConfig['wrong_carrier_route']);
    }

}