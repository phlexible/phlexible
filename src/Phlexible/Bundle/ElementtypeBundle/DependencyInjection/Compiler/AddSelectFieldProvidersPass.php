<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add fields pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddSelectFieldProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $providers = [];
        foreach ($container->findTaggedServiceIds('phlexible_elementtype.select_field_provider') as $id => $attributes) {
            $providers[] = new Reference($id);
        }
        $container->findDefinition('phlexible_elementtype.select_field_providers')->replaceArgument(0, $providers);
    }
}
