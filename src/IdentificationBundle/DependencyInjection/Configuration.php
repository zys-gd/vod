<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.01.19
 * Time: 12:29
 */

namespace IdentificationBundle\DependencyInjection;


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
        $rootNode    = $treeBuilder->root('identification');

        $rootNode
            ->children()
            ->scalarNode('wifi_flow_redirect_route')->isRequired()->end()
            ->scalarNode('homepage_route')->isRequired()->end()
            ->scalarNode('landing_route')->isRequired()->end()
            ->scalarNode('my_account_route')->isRequired()->end()
            ->end();


        return $treeBuilder;
    }
}