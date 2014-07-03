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
use Symfony\Component\DependencyInjection\Reference;

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
        $permissions = $container->findDefinition('phlexible_access_control.permissions');
        foreach ($container->findTaggedServiceIds('phlexible_access_control.permission') as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $permissions->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
