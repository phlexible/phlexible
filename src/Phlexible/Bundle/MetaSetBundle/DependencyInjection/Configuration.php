<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Meta sets configuration.
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
        $rootNode = $treeBuilder->root('phlexible_meta_set');

        $supportedDrivers = array('file', 'custom');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->defaultValue('file')
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('languages')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->defaultValue('en')
                            ->info('Default language for metasets.')
                        ->end()
                        ->scalarNode('available')
                            ->defaultValue('en,de')
                            ->info('Available languages for metasets.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('suggest')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('seperator')
                            ->defaultValue(',')
                            ->info('Separator for suggest entries.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('dumper')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('filesystem_dir')
                            ->defaultValue('%kernel.root_dir%/Resources/metasets')
                            ->info('Filesystem directory for dumped meta sets.')
                        ->end()
                        ->scalarNode('puli_resource_dir')
                            ->defaultValue('/app/metasets')
                            ->info('Puli resource directory for dumped meta sets.')
                        ->end()
                        ->scalarNode('default_type')
                            ->defaultValue('xml')
                            ->info('Default type for dumped meta sets.')
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($v) {return 'custom' === $v['db_driver'] && 'phlexible_meta_set.meta_set_manager.default' === $v['service']['meta_set_manager']; })
                ->thenInvalid('You need to specify your own meta set manager service when using the "custom" driver.')
            ->end();

        $this->addServiceSection($rootNode);

        return $treeBuilder;
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
                        ->scalarNode('meta_set_manager')->defaultValue('phlexible_meta_set.meta_set_manager.default')->end()
                    ->end()
                ->end()
            ->end();
    }
}
