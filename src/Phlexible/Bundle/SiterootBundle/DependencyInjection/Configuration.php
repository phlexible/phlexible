<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Container configuration
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phlexible_siteroot');

        $rootNode
            ->children()
                ->arrayNode('overrides')
                    ->canBeUnset()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('navigation')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('url')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('path')->end()
                                        ->scalarNode('language')->end()
                                        ->scalarNode('default')->end()
                                        ->scalarNode('type')->end()
                                        ->scalarNode('target')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('properties')
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
