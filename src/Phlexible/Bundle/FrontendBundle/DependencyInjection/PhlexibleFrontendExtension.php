<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Frontend extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleFrontendExtension extends Extension
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

        $container->setParameter('frontend.download_rel_nofollow', $config['download_rel_nofollow']);
        $container->setParameter('frontend.request.handler', $config['request']['handler']);
        $container->setParameter('frontend.request.preview_handler', $config['request']['preview_handler']);
        $container->setParameter('frontend.request.edit_handler', $config['request']['edit_handler']);
        $container->setParameter('frontend.request.debug_handler', $config['request']['debug_handler']);
        $container->setParameter('frontend.request.stayssl', $config['request']['stayssl']);
        $container->setParameter('frontend.request.redirect_to_default_siteroot', $config['request']['redirect_to_default_siteroot']);
        $container->setParameter('frontend.request.protocol_businesslogic', $config['request']['protocol_businesslogic']);
        $container->setParameter('frontend.request.force_protocol', $config['request']['force_protocol']);

        if (array_key_exists('FrontendPublishBundle', $container->getParameter('kernel.bundles'))) {
            $container->setDefinition('frontendFrontendPublishItems', new Definition('Phlexible\Bundle\FrontendBundle\FrontendPublish\Items'));
        }
    }
}
