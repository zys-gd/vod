<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 13.01.2019
 * Time: 21:12
 */

namespace ExtrasBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class ExtrasExtension extends ConfigurableExtension
{

    /**
     * Configures the passed container according to the merged configuration.
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('listeners.yml');
        $loader->load('services.yml');
        $loader->load('cache.yml');

        $definition = $container->getDefinition('ExtrasBundle\SignatureCheck\SignatureCheckConfig');

        $definition->setArgument(0, $mergedConfig['signature_check']['request_parameter']);
        $definition->setArgument(1, $mergedConfig['signature_check']['secret_key']);

    }
}