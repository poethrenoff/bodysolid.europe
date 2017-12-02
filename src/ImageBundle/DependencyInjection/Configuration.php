<?php

namespace ImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('image');

        $rootNode
            ->children()
            ->scalarNode('cache_dir')->defaultValue('cache')->end()
            ->scalarNode('cache_dir_mode')->defaultValue('0775')->end()
            ->booleanNode('throw_exception')->defaultFalse()->end()
            ->scalarNode('fallback_image')->defaultNull()->end()
            ->scalarNode('web_dir')->defaultValue('%kernel.root_dir%/../web')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
