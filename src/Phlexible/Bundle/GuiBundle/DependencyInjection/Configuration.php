<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Gui configuration
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
                ->arrayNode('mail')
                    ->isRequired()
                    ->children()
                        ->scalarNode('from_email')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('from_name')->isRequired()->cannotBeEmpty()->end()
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
