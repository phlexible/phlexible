<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Meta sets configuration
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

        $rootNode
            ->children()
                ->arrayNode('languages')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')->defaultValue('en')->end()
                        ->scalarNode('available')->defaultValue('en,de')->end()
                    ->end()
                ->end()
                ->arrayNode('suggest')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('seperator')->defaultValue(',')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
