<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 25/07/17
 * Time: 10:39 AM
 */

namespace SubscriptionBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('subscription');

        $rootNode
            ->children()
                ->scalarNode('resub_not_allowed_route')->isRequired()->end()
                ->scalarNode('action_not_allowed_url')->isRequired()->end()
                ->scalarNode('callback_host')->isRequired()->end()
                ->arrayNode('billing_framework')
                    ->children()
                        ->scalarNode('api_host')->isRequired()->end()
                        ->scalarNode('client_id')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('reporting_tool')
                    ->children()
                        ->scalarNode('api_host')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('cap_tool')
                    ->children()
                    ->arrayNode('notification')
                        ->children()
                            ->scalarNode('mail_to')->isRequired()->end()
                            ->scalarNode('mail_from')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}