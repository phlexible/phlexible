<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add object identity resolvers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddObjectIdentityResolversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $objectIdentityResolver = $container->findDefinition('phlexible_access_control.object_identity_resolver');
        foreach ($container->findTaggedServiceIds('phlexible_access_control.object_identity_resolver') as $id => $attributes) {
            $resolvers[] = new Reference($id);
        }
        $objectIdentityResolver->replaceArgument(0, $resolvers);
    }
}
