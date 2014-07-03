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

/**
 * Add compressors pass
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
        $cssCompressorPriorities = array();
        foreach ($container->findTaggedServiceIds('phlexible_gui.compressor.css') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $cssCompressorPriorities[$priority] = $id;
        }
        krsort($cssCompressorPriorities);
        $container->setAlias('phlexible_gui.compressor.css', current($cssCompressorPriorities));

        // add javascript compressor alias to container
        $javascriptCompressorPriorities = array();
        foreach ($container->findTaggedServiceIds('phlexible_gui.compressor.javascript') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $javascriptCompressorPriorities[$priority] = $id;
        }
        krsort($javascriptCompressorPriorities);
        $container->setAlias('phlexible_gui.compressor.javascript', current($javascriptCompressorPriorities));
    }
}
