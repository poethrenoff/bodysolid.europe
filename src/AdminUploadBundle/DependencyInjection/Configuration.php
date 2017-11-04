<?php

namespace AdminUploadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
        $rootNode = $treeBuilder->root('admin_upload');

        $this->addUploadSection($rootNode);

        return $treeBuilder;
    }

    protected function addUploadSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('directory')->isRequired()->end()
                ->scalarNode('alias')->defaultValue('/upload')->end()
                ->arrayNode('entities')
                    ->useAttributeAsKey('class')
                    ->prototype('array')
                        ->useAttributeAsKey('field')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('fileField')->end()
                                ->scalarNode('directory')->end()
                                ->scalarNode('alias')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
             ->end()
        ;
    }
}
