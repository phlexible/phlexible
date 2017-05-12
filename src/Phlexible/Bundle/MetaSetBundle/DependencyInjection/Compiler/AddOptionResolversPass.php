<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add option resolvers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddOptionResolversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $optionResolvers = array();
        foreach ($container->findTaggedServiceIds('phlexible_meta_set.option_resolver') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('attribute type must be set on phlexible_meta_set.option_resolver');
            }
            $type = $attributes[0]['type'];
            $optionResolvers[$type] = new Reference($id);
        }
        $container->getDefinition('phlexible_meta_set.option_resolver')->replaceArgument(0, $optionResolvers);
    }
}
