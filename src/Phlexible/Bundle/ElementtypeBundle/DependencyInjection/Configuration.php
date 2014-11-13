<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Elementtypes configuration
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
        $rootNode = $treeBuilder->root('phlexible_elementtype');

        $rootNode
            ->children()
                ->arrayNode('fields')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('suggest_separator')->defaultValue(',')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
