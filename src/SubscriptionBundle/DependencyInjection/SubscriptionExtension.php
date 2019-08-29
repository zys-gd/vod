<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 25/07/17
 * Time: 10:32 AM
 */

namespace SubscriptionBundle\DependencyInjection;


use ExtrasBundle\Config\DefinitionReplacer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
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
        $loader->load('billing-framework-integration.yml');
        $loader->load('listeners.yml');
        $loader->load('repositories.yml');
        $loader->load('piwik-integration.yml');
        $loader->load('affiliate.yml');
        $loader->load('fixtures.yml');
        $loader->load('twig.yml');
        $loader->load('campaign_confirmation.yml');
        $loader->load('subscription-voters.yml');
        $loader->load('refund.yml');

        $loader->load('captool-visit.yml');
        $loader->load('captool-commons.yml');
        $loader->load('captool-subscription.yml');
        $loader->load('reporting-tool.yml');
        $loader->load('blacklist.yml');
        $loader->load('action-common.yml');
        $loader->load('subscription-pack.yml');
        $loader->load('complaints.yml');


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
        $loader->load('orange-eg-tpay.yml');
        $loader->load('hutch_id.yml');

        $definition = $container->getDefinition('SubscriptionBundle\CAPTool\Subscription\Notificaton\EmailProvider');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['cap_tool']['notification']['mail_to'], '_cap_notification_mail_to_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['cap_tool']['notification']['mail_from'], '_cap_notification_mail_from_placeholder_');

        $definition = $container->getDefinition('SubscriptionBundle\BillingFramework\BillingOptionsProvider');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['billing_framework']['api_host'], '_billing_api_host_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['billing_framework']['client_id'], '_client_id_placeholder_');

        $definition = $container->getDefinition('SubscriptionBundle\ReportingTool\ReportingToolRequestSender');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['reporting_tool']['api_host'], '_reporting_stats_api_host_placeholder_');

        $definition = $container->getDefinition('SubscriptionBundle\Subscription\Common\RouteProvider');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['resub_not_allowed_route'], '_resub_not_allowed_route_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['action_not_allowed_url'], '_action_not_allowed_url_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['callback_host'], '_callback_host_placeholder_');

        $definition = $container->getDefinition('SubscriptionBundle\Piwik\Senders\RabbitMQ');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['host'], '_rabbitmq_host_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['port'], '_rabbitmq_port_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['user'], '_rabbitmq_user_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['password'], '_rabbitmq_password_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['vhost'], '_rabbitmq_vhost_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['exchange_name'], '_rabbitmq_exchange_name_placeholder_');
        DefinitionReplacer::replacePlaceholder($definition, $mergedConfig['event_tracking']['rabbit_mq']['queue_name'], '_rabbitmq_queue_name_placeholder_');

        $definition = $container->getDefinition('SubscriptionBundle\DataFixtures\ORM\LoadSubscriptionPackData');
        DefinitionReplacer::replacePlaceholder($definition, new Reference($mergedConfig['fixtures']['carrier_fixture']), '_carrier_fixture_service_placeholder_');
    }
}