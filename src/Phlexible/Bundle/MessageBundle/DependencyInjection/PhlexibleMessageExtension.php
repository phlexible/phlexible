<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Messages extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMessageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_message.from_email.address', $config['from_email']['address']);
        $container->setParameter('phlexible_message.from_email.sender_name', $config['from_email']['sender_name']);

        $handlers = [
            new Reference('phlexible_message.handler.message_manager'),
        ];
        if ($config['use_log_handler']) {
            $handlers[] = new Reference('phlexible_message.handler.log');
        }
        if ($container->getParameter('kernel.debug')) {
            $handlers[] = new Reference('phlexible_message.handler.debug');
        }
        $container->findDefinition('phlexible_message.handlers')->replaceArgument(0, $handlers);

        if ($config['message_manager'] === 'doctrine') {
            $loader->load('doctrine_message.yml');
            $container->setAlias('phlexible_message.message_manager', 'phlexible_message.doctrine.message_manager');
        } elseif ($config['message_manager'] === 'elastica') {
            $loader->load('elastica_message.yml');
            $container->setAlias('phlexible_message.message_manager', 'phlexible_message.elastica.message_manager');
        } else {
            throw new \InvalidArgumentException('message_manager needs to be doctrine or elastica');
        }

        $loader->load('doctrine_filter.yml');
        $container->setAlias('phlexible_message.filter_manager', 'phlexible_message.doctrine.filter_manager');

        $loader->load('doctrine_subscription.yml');
        $container->setAlias('phlexible_message.subscription_manager', 'phlexible_message.doctrine.subscription_manager');
    }
}
