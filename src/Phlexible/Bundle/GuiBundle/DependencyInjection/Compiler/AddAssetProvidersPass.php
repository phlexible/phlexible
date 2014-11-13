<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add asset providers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddAssetProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('templating.helper.assets')->addMethodCall('addPackage', [
            'extjs', new Reference('phlexible_gui.templating.asset.extjs_package'),
        ]);

        $providers = [];
        foreach ($container->findTaggedServiceIds('phlexible_gui.asset_provider') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;
            $providers[$priority][] = new Reference($id);
        }
        krsort($providers);
        $flatProviders = [];
        foreach ($providers as $priorityProviders) {
            foreach ($priorityProviders as $priorityProvider) {
                $flatProviders[] = $priorityProvider;
            }
        }
        $providers = $container->findDefinition('phlexible_gui.asset.asset_providers');
        foreach ($flatProviders as $flatProvider) {
            $providers->addMethodCall('addAssetProvider', [new Reference($flatProvider)]);
        }
    }
}
