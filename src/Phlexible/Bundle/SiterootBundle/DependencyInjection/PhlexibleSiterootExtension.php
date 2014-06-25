<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Siteroot extension
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

        if (!empty($config['overrides'])) {
            $overrides = array();
            foreach ($config['overrides'] as $siterootId => $siteroot) {
                $overrides[str_replace('_', '-', $siterootId)] = $siteroot;
            }
            $container->setParameter('phlexible_siteroot.overrides', $overrides);
        }

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_siteroot.siteroot_manager', 'phlexible_siteroot.doctrine.siteroot_manager');
    }
}
