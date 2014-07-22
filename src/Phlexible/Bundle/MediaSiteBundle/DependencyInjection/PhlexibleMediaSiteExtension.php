<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Site extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaSiteExtension extends Extension
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

        $ids = array();
        foreach ($config['sites'] as $name => $siteConfig) {
            $driverId = $siteConfig['driver'];

            $siteDefinition = new Definition('Phlexible\Bundle\MediaSiteBundle\Site\Site', array(
                $siteConfig['id'],
                $siteConfig['root_dir'],
                $siteConfig['quota'],
                new Reference($driverId),
                new Reference('event_dispatcher'),
            ));
            $id = 'phlexible_media_site.site.' . strtolower($name);
            $container->setDefinition($id, $siteDefinition);

            $ids[] = new Reference($id);
        }

        $container->getDefinition('phlexible_media_site.site_manager')->replaceArgument(0, $ids);
    }
}
