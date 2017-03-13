<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add workers pass.
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
                throw new \InvalidArgumentException('Cache Worker priority not set.');
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
        $container->findDefinition('phlexible_media_cache.worker_resolver')->replaceArgument(0, $sortedIds);
    }
}
