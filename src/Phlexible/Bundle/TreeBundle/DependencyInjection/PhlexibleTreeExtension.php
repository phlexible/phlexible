<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Tree extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTreeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');
        $loader->load('mediators.yml');
        $loader->load('content.yml');

        $container->setAlias('phlexible_tree.tree_factory', 'phlexible_tree.doctrine.tree_factory');
        $container->setAlias('phlexible_tree.state_manager', 'phlexible_tree.doctrine.state_manager');

        $container->setAlias('phlexible_tree.content_tree_manager', 'phlexible_tree.content_tree_manager.delegating');
        //$container->setAlias('phlexible_tree.content_tree_manager', 'phlexible_tree.content_tree_manager.xml');
    }
}
