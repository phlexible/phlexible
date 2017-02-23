<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add search providers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddSearchProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $searches = [];
        foreach ($container->findTaggedServiceIds('phlexible_search.provider') as $id => $definition) {
            $searches[] = new Reference($id);
        }
        $container->getDefinition('phlexible_search.search_providers')->replaceArgument(0, $searches);
    }
}
