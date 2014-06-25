<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Messages extension
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

        if ($config['use_log_handler']) {
            $arguments = $container->findDefinition('phlexible_message.handlers')->getArguments();
            if (!isset($arguments[0])) {
                $handlers = array();
            }
            $handlers[] = new Reference('phlexible_message.handler.log');
            $container->findDefinition('phlexible_message.handlers')->replaceArgument(0, $handlers);
        }

        if ($container->getParameter('kernel.debug')) {
            $arguments = $container->findDefinition('phlexible_message.handlers')->getArguments();
            if (!isset($arguments[0])) {
                $handlers = array();
            }
            $handlers[] = new Reference('phlexible_message.handler.debug');
            $container->findDefinition('phlexible_message.handlers')->replaceArgument(0, $handlers);
        }

        if ($config['audit_entities']) {
            $arguments = $container->findDefinition('phlexible_message.handlers')->getArguments();
            if (!isset($arguments[0])) {
                $handlers = array();
            }
            $handlers[] = new Reference('phlexible_message.handler.buffer');
            $container->findDefinition('phlexible_message.handlers')->replaceArgument(0, $handlers);
        }

        $loader->load('doctrine_message.yml');
        $container->setAlias('phlexible_message.message_manager', 'phlexible_message.doctrine.message_manager');

        $loader->load('doctrine_filter.yml');
        $container->setAlias('phlexible_message.filter_manager', 'phlexible_message.doctrine.filter_manager');

        $loader->load('doctrine_subscription.yml');
        $container->setAlias('phlexible_message.subscription_manager', 'phlexible_message.doctrine.subscription_manager');
    }
}
