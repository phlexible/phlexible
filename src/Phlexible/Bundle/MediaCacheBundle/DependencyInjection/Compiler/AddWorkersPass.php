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
        $priorityIds = [];
        foreach ($container->findTaggedServiceIds('phlexible_media_cache.worker') as $id => $definition) {
            if (!isset($definition[0]['priority'])) {
                throw new \InvalidArgumentException("Cache Worker priority not set.");
            }
            $priority = $definition[0]['priority'];
            $priorityIds[$priority][] = new Reference($id);
        }
        krsort($priorityIds);
        $sortedIds = array();
        foreach ($priorityIds as $priority => $ids) {
            foreach ($ids as $id) {
                $sortedIds[] = $id;
            }
        }
        $container->findDefinition('phlexible_media_cache.worker.resolver')->replaceArgument(0, $sortedIds);
    }
}
