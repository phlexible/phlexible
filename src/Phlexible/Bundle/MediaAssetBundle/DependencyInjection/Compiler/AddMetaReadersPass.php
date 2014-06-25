<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add meta readers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddMetaReadersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $resolverDefinition = $container->getDefinition('phlexible_media_asset.reader.meta.resolver');
        $ids = array();
        foreach ($container->findTaggedServiceIds('phlexible_media_asset.meta_reader') as $id => $definition) {
            $ids[] = new Reference($id);
        }
        $resolverDefinition->replaceArgument(0, $ids);
    }
}