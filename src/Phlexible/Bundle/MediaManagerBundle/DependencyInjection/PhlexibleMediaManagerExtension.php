<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\DependencyInjection;

use Phlexible\Bundle\MediaManagerBundle\Entity\File;
use Phlexible\Bundle\MediaManagerBundle\Entity\Folder;
use Phlexible\Component\MediaManager\Volume\ExtendedVolume;
use Phlexible\Component\Volume\Driver\DoctrineDriver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media manager extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaManagerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('meta.yml');
        $loader->load('usage.yml');
        $loader->load('attribute_readers.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $ids = [];
        foreach ($config['volumes'] as $name => $volumeConfig) {
            if (isset($volumeConfig['driver']) && $volumeConfig['driver'] === 'phlexible_media_manager.driver.doctrine') {
                throw new \InvalidArgumentException("Please unset the config value 'phlexible_media_manager.volumes.$name.driver', the driver is created automatically. If your really want to use your own service, rename the service id for the driver.");
            }
            if (!empty($volumeConfig['driver'])) {
                $driverId = $volumeConfig['driver'];
            } else {
                $driverDefinition = new Definition(DoctrineDriver::class, [
                    new Reference("doctrine.orm.entity_manager"),
                    new Reference("phlexible_media_manager.hash_calculator"),
                    Folder::class,
                    File::class,
                    $volumeConfig['features'],
                ]);
                $driverId = 'phlexible_media_manager.driver.'.strtolower($name);
                $container->setDefinition($driverId, $driverDefinition);
            }

            $volumeDefinition = new Definition(ExtendedVolume::class, [
                $volumeConfig['id'],
                rtrim($volumeConfig['root_dir'], '/').'/',
                $volumeConfig['quota'],
                new Reference($driverId, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, false),
                new Reference('event_dispatcher'),
            ]);
            $volumeId = 'phlexible_media_manager.volume.'.strtolower($name);
            $container->setDefinition($volumeId, $volumeDefinition);

            $ids[$name] = new Reference($volumeId);
        }

        $container->getDefinition('phlexible_media_manager.volume_manager')->replaceArgument(0, $ids);

        $container->setParameter('phlexible_media_manager.portlet.style', $config['portlet']['style']);
        $container->setParameter('phlexible_media_manager.portlet.num_items', $config['portlet']['num_items']);
        $container->setParameter('phlexible_media_manager.files.view', $config['files']['view']);
        $container->setParameter('phlexible_media_manager.files.num_files', $config['files']['num_files']);
        $container->setParameter('phlexible_media_manager.upload.enable_upload_sort', $config['upload']['enable_upload_sort']);
        $container->setParameter('phlexible_media_manager.upload.disable_flash', $config['upload']['disable_flash']);
        $container->setParameter('phlexible_media_manager.delete_policy', $config['delete_policy']);
        $container->setParameter('phlexible_media_manager.metaset_mapping', $config['metaset_mapping']);
        $container->setParameter('phlexible_media_manager.wizard_mapping', $config['upload']['wizard']);

        $container->setAlias('phlexible_media_manager.folder_usage_manager', 'phlexible_media_manager.doctrine.folder_usage_manager');
        $container->setAlias('phlexible_media_manager.file_usage_manager', 'phlexible_media_manager.doctrine.file_usage_manager');
        $container->setAlias('phlexible_media_manager.folder_meta_data_manager', 'phlexible_media_manager.doctrine.folder_meta_data_manager');
        $container->setAlias('phlexible_media_manager.file_meta_data_manager', 'phlexible_media_manager.doctrine.file_meta_data_manager');
    }
}
