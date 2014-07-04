<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Meta set extension
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
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_meta_set.languages.default', $config['languages']['default']);
        $container->setParameter('phlexible_meta_set.languages.available', $config['languages']['available']);
        $container->setParameter('phlexible_meta_set.suggest.seperator', $config['suggest']['seperator']);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_meta_set.meta_set_manager', 'phlexible_meta_set.doctrine.meta_set_manager');
    }
}
