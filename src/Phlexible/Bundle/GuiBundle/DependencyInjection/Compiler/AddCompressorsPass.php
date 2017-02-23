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

/**
 * Add compressors pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddCompressorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // add css compressor alias to container
        $cssCompressorPriorities = [];
        foreach ($container->findTaggedServiceIds('phlexible_gui.compressor.css') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $cssCompressorPriorities[$priority] = $id;
        }
        krsort($cssCompressorPriorities);
        $container->setAlias('phlexible_gui.compressor.css', current($cssCompressorPriorities));

        // add javascript compressor alias to container
        $javascriptCompressorPriorities = [];
        foreach ($container->findTaggedServiceIds('phlexible_gui.compressor.javascript') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $javascriptCompressorPriorities[$priority] = $id;
        }
        krsort($javascriptCompressorPriorities);
        $container->setAlias('phlexible_gui.compressor.javascript', current($javascriptCompressorPriorities));
    }
}
