<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add workers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddWorkersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = array();
        foreach ($container->findTaggedServiceIds('phlexible_media_cache.worker') as $id => $definition) {
            $ids[] = new Reference($id);
        }
        $container->findDefinition('phlexible_media_cache.worker.resolver')->replaceArgument(0, $ids);
    }
}