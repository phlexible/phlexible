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
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Add fields pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFieldsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $fields = [];
        foreach ($container->findTaggedServiceIds('phlexible_elementtype.field') as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException('Tag phlexible_elementtype.field needs alias');
            }

            $definition = $container->findDefinition($id);
            $fields[$attributes[0]['alias']] = $definition->getClass();
        }
        $container->findDefinition('phlexible_elementtype.field.registry')->replaceArgument(0, $fields);
    }
}
