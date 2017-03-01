<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media manager configuration.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phlexible_media_manager');

        $rootNode
            ->children()
                ->arrayNode('metaset_mapping')
                    ->useAttributeAsKey('metaset')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('category')->end()
                            ->scalarNode('name')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('volumes')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('id')->end()
                            ->scalarNode('driver')->defaultValue('phlexible_media_manager.driver.doctrine')->end()
                            ->scalarNode('root_dir')->end()
                            ->integerNode('quota')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('portlet')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('style')->defaultValue('large')->end()
                        ->integerNode('num_items')->defaultValue(10)->end()
                    ->end()
                ->end()
                ->arrayNode('files')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('view')->defaultValue('tile')->end()
                        ->integerNode('num_files')->defaultValue(50)->end()
                    ->end()
                ->end()
                ->arrayNode('upload')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable_upload_sort')->defaultValue(false)->end()
                        ->booleanNode('disable_flash')->defaultValue(false)->end()
                        ->arrayNode('wizard')
                            ->children()
                                ->arrayNode('categories')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('types')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('delete_policy')
                    ->defaultValue('hide_old')
                    ->validate()
                        ->ifNotInArray(['hide_old', 'delete_old', 'delete_all'])
                        ->thenInvalid('delete_policy has to be one of "hide_old", "delete_old", "delete_all"')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
