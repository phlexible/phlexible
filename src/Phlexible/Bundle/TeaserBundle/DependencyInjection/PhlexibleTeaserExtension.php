<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Teaser extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTeaserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');
        $loader->load('mediator.yml');
        $loader->load('content.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter(
            'phlexible_teaser.catch.use_master_language_as_fallback',
            $config['catch']['use_master_language_as_fallback']
        );

        $container->setAlias('phlexible_teaser.teaser_manager', 'phlexible_teaser.doctrine.teaser_manager');
        $container->setAlias('phlexible_teaser.teaser_service', 'phlexible_teaser.doctrine.teaser_manager');
        $container->setAlias('phlexible_teaser.catch_manager', 'phlexible_teaser.doctrine.catch_manager');
        $container->setAlias('phlexible_teaser.state_manager', 'phlexible_teaser.doctrine.state_manager');
    }
}
