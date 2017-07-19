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

        $supportedDrivers = ['local', 'custom'];

        $rootNode
            ->children()
                ->booleanNode('immediately_cache_system_templates')->defaultValue(true)->end()
                ->arrayNode('storages')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('driver')
                                ->isRequired()
                                ->validate()
                                    ->ifNotInArray($supportedDrivers)
                                    ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
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

        return $treeBuilder;
    }
}
