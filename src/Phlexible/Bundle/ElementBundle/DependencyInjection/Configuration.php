<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Elements configuration
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
        $rootNode = $treeBuilder->root('phlexible_element');

        $rootNode
            ->children()
                ->arrayNode('create')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('restricted')->defaultValue(false)->end()
                        ->booleanNode('use_multilanguage')->defaultValue(false)->end()
                    ->end()
                ->end()
                ->arrayNode('portlet')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('num_items')->defaultValue(10)->end()
                    ->end()
                ->end()
                ->arrayNode('publish')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('comment_required')->defaultValue(false)->end()
                        ->booleanNode('confirm_required')->defaultValue(false)->end()
                        ->booleanNode('cross_language_publish_offline')->defaultValue(false)->end()
                    ->end()
                ->end()
                ->arrayNode('tree')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('sync_page')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
