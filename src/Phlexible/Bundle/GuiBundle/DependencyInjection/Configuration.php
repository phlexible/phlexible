<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Gui configuration.
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
        $rootNode = $treeBuilder->root('phlexible_gui');

        $rootNode
            ->children()
                ->arrayNode('project')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('title')->defaultValue('phlexible')->end()
                        ->scalarNode('version')->defaultValue('1.0.0-dev')->end()
                        ->scalarNode('url')->defaultValue('phlexible.net')->end()
                    ->end()
                ->end()
                ->arrayNode('ext')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')->cannotBeEmpty()->defaultValue('ext-2.3.0')->end()
                    ->end()
                ->end()
                ->arrayNode('languages')
                    ->isRequired()
                    ->children()
                        ->scalarNode('default')->defaultValue('en')->cannotBeEmpty()->end()
                        ->scalarNode('available')->defaultValue('de', 'en')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
