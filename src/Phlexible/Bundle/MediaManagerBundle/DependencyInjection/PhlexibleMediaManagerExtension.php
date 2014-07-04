<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media manager extension
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

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media_manager.portlet.style', $config['portlet']['style']);
        $container->setParameter('phlexible_media_manager.portlet.num_items', $config['portlet']['num_items']);
        $container->setParameter('phlexible_media_manager.files.view', $config['files']['view']);
        $container->setParameter('phlexible_media_manager.files.num_files', $config['files']['num_files']);
        $container->setParameter('phlexible_media_manager.upload.enable_upload_sort', $config['upload']['enable_upload_sort']);
        $container->setParameter('phlexible_media_manager.upload.disable_flash', $config['upload']['disable_flash']);
        $container->setParameter('phlexible_media_manager.delete_policy', $config['delete_policy']);

        $container->setAlias('phlexible_media_manager.folder_usage_manager', 'phlexible_media_manager.doctrine.folder_usage_manager');
        $container->setAlias('phlexible_media_manager.file_usage_manager', 'phlexible_media_manager.doctrine.file_usage_manager');
        $container->setAlias('phlexible_media_manager.folder_meta_data_manager', 'phlexible_media_manager.doctrine.folder_meta_data_manager');
        $container->setAlias('phlexible_media_manager.file_meta_data_manager', 'phlexible_media_manager.doctrine.file_meta_data_manager');
    }
}
