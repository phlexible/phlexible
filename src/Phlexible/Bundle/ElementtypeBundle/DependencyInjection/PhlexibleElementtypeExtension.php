<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Elementtype extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementtypeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');
        $loader->load('file.yml');
        $loader->load('fields.yml');

        $container->setAlias('phlexible_elementtype.viability_manager', 'phlexible_elementtype.doctrine.viability_manager');
        $container->setAlias('phlexible_elementtype.elementtype_manager', 'phlexible_elementtype.file.elementtype_manager');
    }
}
