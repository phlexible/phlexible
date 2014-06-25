<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Tasks configuration
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
        $rootNode = $treeBuilder->root('phlexible_task');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('portlet')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('num_items')->defaultValue(10)->end()
                    ->end()
                ->end()
                ->booleanNode('mail_on_close')->defaultValue(true)->end()
            ->end();

        return $treeBuilder;
    }
}
