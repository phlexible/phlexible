<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add permissions pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddPermissionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // add css compressor alias to container
        $rights = array();
        foreach ($container->findTaggedServiceIds('phlexible_access_control.permission') as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $class = $definition->getClass();
            $provider = new $class();
            $rights = array_merge($rights, $provider->getPermissions());
        }
        $container->findDefinition('phlexible_access_control.permissions')->replaceArgument(0, $rights);
    }
}
