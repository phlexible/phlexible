<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('phlexible_tree');

        $rootNode
            ->children()
                ->arrayNode('mediator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('element_sluggable_voter')->defaultValue('phlexible_tree.element_mediator.sluggable_voter.default')->end()
                        ->scalarNode('element_viewable_voter')->defaultValue('phlexible_tree.element_mediator.viewable_voter.default')->end()
                    ->end()
                ->end()
                ->arrayNode('router')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('handler_service')->defaultValue('phlexible_tree.router.default_handler')->end()
                        ->scalarNode('url_generator_service')->defaultValue('phlexible_tree.router.default_url_generator')->end()
                        ->scalarNode('request_matcher_service')->defaultValue('phlexible_tree.router.default_request_matcher')->end()
                    ->end()
                ->end()
                ->arrayNode('patterns')
                    ->canBeUnset()
                    ->prototype('scalar')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
