<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Siteroot extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleSiterootExtension extends Extension
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

        $mappings = [];
        if (!empty($config['mappings'])) {
            foreach ($config['mappings'] as $mapping) {
                $mappings[$mapping['to']] = $mapping['from'];
            }
        }

        $container->setParameter('phlexible_siteroot.mappings', $mappings);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_siteroot.siteroot_manager', 'phlexible_siteroot.doctrine.siteroot_manager');
    }
}
