<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media cache extension
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

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media_cache.asynchronous_system_cache', $config['asynchronous_system_cache']);

        $ids = array();
        foreach ($config['storages'] as $name => $storageConfig) {
            if (!isset($storageConfig['id']) || !isset($storageConfig['options'])) {
                throw new InvalidArgumentException('Storage config needs id and options.');
            }
            $storageId = $storageConfig['id'];
            $storage = $container->findDefinition($storageId);
            $storage->replaceArgument(0, $storageConfig['options']);
            $ids[$name] = new Reference($storageId);
        }

        $container->getDefinition('phlexible_media_cache.storage_manager')->replaceArgument(0, $ids);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_media_cache.cache_manager', 'phlexible_media_cache.doctrine.cache_manager');
        $container->setAlias('phlexible_media_cache.queue_manager', 'phlexible_media_cache.doctrine.queue_manager');
    }
}
