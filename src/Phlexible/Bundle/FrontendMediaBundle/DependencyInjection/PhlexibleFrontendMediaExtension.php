<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Frontend media extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleFrontendMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('usage_updaters.yml');

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('DistributionListsBundle', $bundles)) {
            $loader->load('distributionlists.yml');
        }

        if (array_key_exists('FrontendPublishBundle', $bundles)) {
            $loader->load('frontendpublish.yml');
        }
    }
}
