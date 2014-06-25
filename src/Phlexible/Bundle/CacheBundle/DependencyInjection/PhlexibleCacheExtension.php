<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Cache extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleCacheExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($config, $container);
        $processedConfiguration = $this->processConfiguration($configuration, $config);

        if (!empty($processedConfiguration['namespaces'])) {
            $container->setAlias('phlexible_cache.managed_cache', 'phlexible_cache.doctrine.' . strtolower(key($processedConfiguration['namespaces'])));

            foreach ($processedConfiguration['namespaces'] as $name => $config) {
                $namespace = empty($config['namespace']) ? $name : $config['namespace'];
                $definition = new Definition('Doctrine\Common\Cache\\' . ucfirst($config['type']) . 'Cache');
                $definition
                    ->addMethodCall('setNamespace', array($namespace));

                switch ($config['type']) {
                    case 'Memcache':
                    case 'Memcached':
                        if (empty($config['id'])) {
                            $memcacheId = 'phlexible_cache.doctrine.memcache.' . strtolower($name);
                            $memcacheHost = $config['host'];
                            $memcachePort = $config['port'];
                            $container->setDefinition($memcacheId, new Definition($config['type']))
                                ->addMethodCall('addServer', array($memcacheHost, $memcachePort));
                        } else {
                            $memcacheId = $config['id'];
                        }
                        $definition->addMethodCall('set' . ucfirst($config['type']), array($memcacheId));
                        break;
                    case 'Filesystem':
                    case 'PhpFile':
                        $directory = !empty($config['directory']) ? $config['directory'] : '%kernel.cache_dir%/doctrine/cache/' . strtolower($name) . '/';
                        $extension = !empty($config['extension']) ? $config['extension'] : null;

                        $definition->setArguments(array($directory, $extension));

                        break;
                }
                $id = 'phlexible_cache.doctrine.' . strtolower($name);
                $container->setDefinition($id, $definition);

                $container->findDefinition('phlexible_cache.caches')
                    ->addMethodCall('addCache', array($name, new Reference($id)));
            }
        }
    }
}
