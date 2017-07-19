<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media cache configuration.
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
        $rootNode = $treeBuilder->root('phlexible_media_cache');

        $supportedDbDrivers = ['orm', 'custom'];

        $rootNode
            ->children()
                ->booleanNode('immediately_cache_system_templates')->defaultValue(true)->end()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDbDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDbDrivers))
                    ->end()
                    ->defaultValue('orm')
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end()
            // Using the custom driver requires changing the manager services
            ->validate()
                ->ifTrue(function ($v) {return 'custom' === $v['db_driver'] && 'phlexible_media_cache.cache_manager.default' === $v['service']['cache_manager']; })
                ->thenInvalid('You need to specify your own cache manager service when using the "custom" driver.')
            ->end();;

        $this->addStoragesSection($rootNode);
        $this->addServiceSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addStoragesSection(ArrayNodeDefinition $node)
    {
        $supportedStorageDrivers = ['local', 'custom'];

        $node
            ->children()
                ->arrayNode('storages')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('driver')
                                ->isRequired()
                                ->validate()
                                    ->ifNotInArray($supportedStorageDrivers)
                                    ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedStorageDrivers))
                                ->end()
                            ->end()
                            ->scalarNode('service')->end()
                            ->scalarNode('storage_dir')->end()
                        ->end()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return 'custom' === $v['driver'] && empty($v['service']);
                            })
                            ->thenInvalid('You need to specify a service when using the "custom" driver.')
                        ->end()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return 'local' === $v['driver'] && !empty($v['service']);
                            })
                            ->thenInvalid('You can not specify a service when using the "local" driver.')
                        ->end()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return 'local' === $v['driver'] && empty($v['storage_dir']);
                            })
                            ->thenInvalid('You need t specify the storage_dir when using the "local" driver.')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cache_manager')->defaultValue('phlexible_media_cache.cache_manager.default')->end()
                    ->end()
                ->end()
            ->end();
    }
}
