<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Teasers configuration
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
        $rootNode = $treeBuilder->root('phlexible_teaser');

        $rootNode
            ->children()
            ->arrayNode('catch')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('use_master_language_as_fallback')->defaultValue(false)->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
