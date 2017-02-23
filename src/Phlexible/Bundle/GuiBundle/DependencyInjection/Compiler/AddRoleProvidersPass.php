<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add roles pass.
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
