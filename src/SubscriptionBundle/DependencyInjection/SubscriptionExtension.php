<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 25/07/17
 * Time: 10:32 AM
 */

namespace SubscriptionBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class SubscriptionExtension extends ConfigurableExtension
{
    /**
     * Configures the passed container according to the merged configuration.
     *
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');
        $loader->load('action-billing-callback.yml');
        $loader->load('action-billing-notif.yml');
        $loader->load('action-subscribe.yml');
        $loader->load('action-unsubscribe.yml');
        $loader->load('action-renew.yml');
        $loader->load('action-mass-renew.yml');
        $loader->load('action-subscribe-back.yml');
        $loader->load('admin.yml');
        $loader->load('billing-framework-integration.yml');
        $loader->load('enqueue-integration.yml');
        $loader->load('listeners.yml');
        $loader->load('repositories.yml');
        $loader->load('controllers.yml');
        $loader->load('cron.yml');
        $loader->load('piwik-integration.yml');
        $loader->load('affiliate.yml');
        $loader->load('fixtures.yml');
        $loader->load('twig.yml');
        $loader->load('campaign_confirmation.yml');


        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/notifications')
        );
        $loader->load('parameters.yml');

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/carriers')
        );
        //$loader->load('orange-eg.yml');
        //$loader->load('orange-tn.yml');
        $loader->load('etisalat-eg.yml');
        $loader->load('telenor-pk.yml');
        $loader->load('jazz-pk.yml');
        $loader->load('vodafone-eg-tpay.yml');


        $definition = $container->getDefinition('SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter');

        $definition->replaceArgument(5, $mergedConfig['sub_not_allowed_route']);
        $definition->replaceArgument(6, $mergedConfig['blacklisted_user_route']);


        $definition = $container->getDefinition('SubscriptionBundle\Service\Action\Subscribe\Common\CommonFlowHandler');

        $definition->replaceArgument(14, $mergedConfig['resub_not_allowed_route']);
    }
}