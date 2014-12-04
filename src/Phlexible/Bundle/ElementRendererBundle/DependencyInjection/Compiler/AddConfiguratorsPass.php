<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add configurators pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddConfiguratorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $configurators = array();
        foreach ($container->findTaggedServiceIds('phlexible_element_renderer.configurator') as $id => $attributes) {
            if (!isset($attributes[0]['priority'])) {
                throw new \InvalidArgumentException("Missing priority");
            }
            $configurators[$attributes[0]['priority']][] = new Reference($id);
        }
        krsort($configurators);
        $flatConfigurators = array();
        foreach ($configurators as $configurator) {
            $flatConfigurators = array_merge($flatConfigurators, $configurator);
        }

        $container->findDefinition('phlexible_element_renderer.configurator')->replaceArgument(0, $flatConfigurators);
    }
}
