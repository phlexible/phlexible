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
        $rootNode = $treeBuilder->root('phlexible_siteroot');

        $rootNode
            ->children()
                ->arrayNode('mappings')
                    ->canBeUnset()
                    ->prototype('array')
                        ->children()
                            ->arrayNode('from')
                                ->beforeNormalization()->ifString()->then(function ($v) { return array($v); })->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('to')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
