<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection;

use Phlexible\Component\MediaCache\Storage\LocalStorage;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media cache extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaCacheExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('workers.yml');
        $loader->load('batch.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media_cache.immediately_cache_system_templates', $config['immediately_cache_system_templates']);

        $ids = [];
        foreach ($config['storages'] as $name => $storageConfig) {
            if ($storageConfig['driver'] === 'local') {
                $storageDefinition = new Definition(LocalStorage::class, [
                    $storageConfig['storage_dir'],
                    new Reference('phlexible_media_cache.cache_manager')
                ]);
                $storageId = 'phlexible_media_cache.storage.'.$name;
                $container->setDefinition($storageId, $storageDefinition);
            } elseif ($storageConfig['driver'] === 'custom') {
                $storageId = $storageConfig['service'];
            } else {
                continue;
            }

            $ids[$name] = new Reference($storageId);
        }

        $container->getDefinition('phlexible_media_cache.storage_manager')
            ->replaceArgument(0, $ids);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_media_cache.cache_manager', 'phlexible_media_cache.doctrine.cache_manager');
    }
}
