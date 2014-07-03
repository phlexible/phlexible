<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media manager configuration
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
        $rootNode = $treeBuilder->root('phlexible_media_manager');

        $rootNode
            ->children()
            ->arrayNode('portlet')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('style')->defaultValue('large')->end()
            ->integerNode('num_items')->defaultValue(10)->end()
            ->end()
            ->end()
            ->arrayNode('files')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('view')->defaultValue('tile')->end()
            ->integerNode('num_files')->defaultValue(10)->end()
            ->end()
            ->end()
            ->arrayNode('upload')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enable_upload_sort')->defaultValue(false)->end()
            ->booleanNode('disable_flash')->defaultValue(false)->end()
            ->end()
            ->end()
            ->scalarNode('delete_policy')
            ->defaultValue('hide_old')
            ->validate()
            ->ifNotInArray(array('hide_old', 'delete_old', 'delete_all'))
            ->thenInvalid('delete_policy has to be one of "hide_old", "delete_old", "delete_all"')
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}