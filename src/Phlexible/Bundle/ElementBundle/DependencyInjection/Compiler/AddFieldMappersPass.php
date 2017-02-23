<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add field mappers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFieldMappersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $mappers = [];
        foreach ($container->findTaggedServiceIds('phlexible_element.field_mapper') as $id => $attributes) {
            $mappers[] = new Reference($id);
        }
        $mapper = $container->findDefinition('phlexible_element.field_mapper');
        $mapper->replaceArgument(2, $mappers);
    }
}
