<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Task extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTaskExtension extends Extension
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

        $container->setParameter('phlexible_task.portlet.num_items', $config['portlet']['num_items']);
        $container->setParameter('phlexible_task.mail_on_close', $config['mail_on_close']);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_task.task_manager', 'phlexible_task.doctrine.task_manager');
    }
}
