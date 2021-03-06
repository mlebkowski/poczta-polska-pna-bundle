<?php

namespace Nassau\PocztaPolskaPnaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('poczta_polska_pna');

        $rootNode->children()->scalarNode('index_name')->defaultNull();
        $rootNode->children()->scalarNode('algolia_client')->defaultValue('goldenline_algolia.client');

        return $treeBuilder;
    }
}
