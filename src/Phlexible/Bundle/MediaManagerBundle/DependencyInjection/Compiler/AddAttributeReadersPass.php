<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add attribute readers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddAttributeReadersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $chainReaderDefinition = $container->getDefinition('phlexible_media_manager.attribute_reader');
        $ids = [];
        foreach ($container->findTaggedServiceIds('phlexible_media_manager.attribute_reader') as $id => $definition) {
            $ids[] = new Reference($id);
        }
        $chainReaderDefinition->replaceArgument(0, $ids);
    }
}
