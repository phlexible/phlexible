<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Users configuration.
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
        $rootNode = $treeBuilder->root('phlexible_user');

        $rootNode
            ->children()
                ->scalarNode('user_to_array_transformer')->defaultValue('phlexible.user.phlexible_user_to_array_transformer')->end()
                ->scalarNode('user_request_applier')->defaultValue('phlexible_user.phlexible_user_request_applier')->end()
                ->scalarNode('system_user_id')->defaultValue('be0803d2-e580-11e2-b137-19a2e180dfdd')->end()
                ->scalarNode('everyone_group_id')->defaultValue('48fdb07b-b164-4d07-83be-5bf7c0a8005b')->end()
                ->arrayNode('password')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('min_length')->defaultValue(8)->info('Minimal password length.')->end()
                    ->end()
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('language')->defaultValue('de')->info('Default language for new users.')->end()
                        ->scalarNode('theme')->defaultValue('default')->info('Default theme for new users.')->end()
                        ->booleanNode('force_password_change')->defaultValue(false)->info('Default value of force password change flag for new users.')->end()
                        ->booleanNode('cant_change_password')->defaultValue(false)->info('Default value of cant change password flag for new users.')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
