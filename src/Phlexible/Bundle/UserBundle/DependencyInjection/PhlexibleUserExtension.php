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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * User extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('phlexible_user.user.system_user_id', $config['system_user_id']);
        $container->setParameter('phlexible_user.group.everyone_group_id', $config['everyone_group_id']);
        $container->setParameter('phlexible_user.password.min_length', $config['password']['min_length']);
        $container->setParameter('phlexible_user.defaults.language', $config['defaults']['language']);
        $container->setParameter('phlexible_user.defaults.theme', $config['defaults']['theme']);
        $container->setParameter('phlexible_user.defaults.force_password_change', $config['defaults']['force_password_change']);
        $container->setParameter('phlexible_user.defaults.cant_change_password', $config['defaults']['cant_change_password']);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_user.group_manager', 'phlexible_user.doctrine.group_manager');
        $container->setAlias('phlexible_user.user_manager', 'phlexible_user.doctrine.user_manager');
    }
}
