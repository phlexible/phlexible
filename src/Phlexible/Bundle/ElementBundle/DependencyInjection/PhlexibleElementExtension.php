<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Element extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('field_mappers.yml');
        $loader->load('link_extractors.yml');
        $loader->load('content.yml');
        $loader->load('configurators.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_element.create.restricted', $config['create']['restricted']);
        $container->setParameter('phlexible_element.create.use_multilanguage', $config['create']['use_multilanguage']);
        $container->setParameter('phlexible_element.portlet.num_items', $config['portlet']['num_items']);
        $container->setParameter('phlexible_element.publish.comment_required', $config['publish']['comment_required']);
        $container->setParameter('phlexible_element.publish.confirm_required', $config['publish']['confirm_required']);
        $container->setParameter(
            'phlexible_element.publish.cross_language_publish_offline',
            $config['publish']['cross_language_publish_offline']
        );
        $container->setParameter('phlexible_element.tree.sync_page', $config['tree']['sync_page']);

        $loader->load('doctrine.yml');
        $container->setAlias('phlexible_element.element_manager', 'phlexible_element.doctrine.element_manager');
        $container->setAlias('phlexible_element.element_version_manager', 'phlexible_element.doctrine.element_version_manager');
        $container->setAlias('phlexible_element.element_structure_manager', 'phlexible_element.doctrine.element_structure_manager');
        $container->setAlias('phlexible_element.element_source_manager', 'phlexible_element.doctrine.element_source_manager');
        $container->setAlias('phlexible_element.element_lock_manager', 'phlexible_element.doctrine.element_lock_manager');
        $container->setAlias('phlexible_element.element_history_manager', 'phlexible_element.doctrine.element_history_manager');
        $container->setAlias('phlexible_element.element_link_manager', 'phlexible_element.doctrine.element_link_manager');
    }
}
