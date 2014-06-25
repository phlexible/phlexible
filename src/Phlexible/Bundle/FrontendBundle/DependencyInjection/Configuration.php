<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Frontend configuration
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
        $rootNode = $treeBuilder->root('phlexible_frontend');

        $rootNode
            ->children()
                ->booleanNode('download_rel_nofollow')->defaultValue(false)->end()
                ->arrayNode('request')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('handler')->defaultValue('Makeweb_Frontend_Request_Handler_IdBased')->end()
                        ->scalarNode('preview_handler')->defaultValue('Makeweb_Frontend_Request_Handler_Preview')->end()
                        ->scalarNode('edit_handler')->defaultValue('Makeweb_Frontend_Request_Handler_Preview')->end()
                        ->scalarNode('debug_handler')->defaultValue('Makeweb_Frontend_Request_Handler_Edit')->end()
                        ->booleanNode('stayssl')->defaultValue(false)->end()
                        ->booleanNode('redirect_to_default_siteroot')->defaultValue(true)->end()
                        ->scalarNode('protocol_businesslogic')
                            ->defaultValue('auto')
                            ->validate()
                                ->ifNotInArray(array('auto', 'http', 'https'))
                                ->thenInvalid('protocol_businesslogic policy has to be one of "auto", "http", "https"')
                            ->end()
                        ->end()
                        ->scalarNode('force_protocol')
                            ->defaultValue('auto')
                            ->validate()
                                ->ifNotInArray(array('auto', 'http', 'https'))
                                ->thenInvalid('force_protocol has to be one of "auto", "http", "https"')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
