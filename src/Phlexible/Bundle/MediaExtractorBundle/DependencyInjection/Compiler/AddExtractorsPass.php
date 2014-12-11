<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add extractors pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddExtractorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = [];
        foreach ($container->findTaggedServiceIds('phlexible_media_extractor.extractor') as $id => $definition) {
            $ids[] = new Reference($id);
        }
        $definition = $container->getDefinition('phlexible_media_extractor.extractor.resolver');
        $definition->replaceArgument(0, $ids);
    }
}
