<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietery
 */

namespace Phlexible\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Users configuration
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
                ->scalarNode('system_user_id')->defaultValue('be0803d2-e580-11e2-b137-19a2e180dfdd')->end()
                ->scalarNode('everyone_group_id')->defaultValue('48fdb07b-b164-4d07-83be-5bf7c0a8005b')->end()
                ->arrayNode('password')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('min_length')->defaultValue(8)->info('Minimal password length.')->end()
                        ->integerNode('expire_after_days')->defaultValue(0)->info('Passwords expire and have to be reset after this number of day.')->end()
                    ->end()
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('language')->defaultValue('de')->info('Default language for new users.')->end()
                        ->scalarNode('theme')->defaultValue('default')->info('Default theme for new users.')->end()
                        ->booleanNode('force_password_change')->defaultValue(false)->info('Default value of force password change flag for new users.')->end()
                        ->booleanNode('cant_change_password')->defaultValue(false)->info('Default value of cant change password flag for new users.')->end()
                        ->booleanNode('password_doesnt_expire')->defaultValue(false)->info('Default value of password doesnt expire flag for new users.')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
