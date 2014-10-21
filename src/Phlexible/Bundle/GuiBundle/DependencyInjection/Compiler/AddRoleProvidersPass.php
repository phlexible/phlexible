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
 * Add roles pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddRoleProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $roleHierarchyFactory = $container->findDefinition('phlexible_gui.security.role_hierarchy_factory');
        foreach ($container->findTaggedServiceIds('phlexible_gui.role_provider') as $id => $attributes) {
            $roleHierarchyFactory->addMethodCall('addRoleProvider', [new Reference($id)]);
        }
    }
}
