<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Gui extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleGuiExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('assets.yml');
        $loader->load('twig.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_gui.project.title', $config['project']['title']);
        $container->setParameter('phlexible_gui.project.version', $config['project']['version']);
        $container->setParameter('phlexible_gui.project.url', $config['project']['url']);
        $container->setParameter('phlexible_gui.languages.default', $config['languages']['default']);
        $container->setParameter('phlexible_gui.languages.available', $config['languages']['available']);
        $container->setParameter('phlexible_gui.mail.from_email', $config['mail']['from_email']);
        $container->setParameter('phlexible_gui.mail.from_name', $config['mail']['from_name']);
    }
}
