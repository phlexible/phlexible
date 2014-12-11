<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add attribute readers pass
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
