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
                ->arrayNode('mailer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('from_email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('address')->isRequired()->cannotBeEmpty()->defaultValue('%phlexible_gui.mail.from_email%')->end()
                                ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->defaultValue('phlexible')->end()
                            ->end()
                        ->end()
                        ->arrayNode('new_task')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('PhlexibleTaskBundle:NewTask:email.txt.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('update')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('PhlexibleTaskBundle:Update:email.txt.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('mail_on_close')->defaultValue(true)->end()
            ->end();

        return $treeBuilder;
    }
}
