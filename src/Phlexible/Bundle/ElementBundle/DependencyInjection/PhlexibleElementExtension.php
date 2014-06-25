<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Element extension
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
        $loader->load('tasks.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_element.create.restricted', $config['create']['restricted']);
        $container->setParameter('phlexible_element.create.use_multilanguage', $config['create']['use_multilanguage']);
        $container->setParameter('phlexible_element.portlet.num_items', $config['portlet']['num_items']);
        $container->setParameter('phlexible_element.publish.comment_required', $config['publish']['comment_required']);
        $container->setParameter('phlexible_element.publish.confirm_required', $config['publish']['confirm_required']);
        $container->setParameter('phlexible_element.publish.cross_language_publish_offline', $config['publish']['cross_language_publish_offline']);
        $container->setParameter('phlexible_element.tree.sync_page', $config['tree']['sync_page']);
        $container->setParameter('phlexible_element.context.enabled', $config['context']['enabled']);
        $container->setParameter('phlexible_element.context.default_country', $config['context']['default_country']);
        $container->setParameter('phlexible_element.context.countries', $config['context']['countries']);
    }
}
