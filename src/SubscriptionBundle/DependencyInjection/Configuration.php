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
                ->scalarNode('sub_not_allowed_route')
                ->end()
            ->end();

        return $treeBuilder;
    }
}