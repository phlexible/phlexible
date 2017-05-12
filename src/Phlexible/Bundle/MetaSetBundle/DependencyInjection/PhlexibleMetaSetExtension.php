<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Meta set extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMetaSetExtension extends Extension
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

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.yml', $config['db_driver']));
            $container->setParameter($this->getAlias().'.backend_type_'.$config['db_driver'], true);

            $container->setParameter('phlexible_meta_set.dumper.filesystem_dir', $config['dumper']['filesystem_dir']);
            $container->setParameter('phlexible_meta_set.dumper.puli_resource_dir', $config['dumper']['puli_resource_dir']);
            $container->setParameter('phlexible_meta_set.dumper.default_type', $config['dumper']['default_type']);
        }

        $container->setParameter('phlexible_meta_set.languages.default', $config['languages']['default']);
        $container->setParameter('phlexible_meta_set.languages.available', $config['languages']['available']);
        $container->setParameter('phlexible_meta_set.suggest.seperator', $config['suggest']['seperator']);

        $container->setAlias('phlexible_meta_set.meta_set_manager', $config['service']['meta_set_manager']);
    }
}
