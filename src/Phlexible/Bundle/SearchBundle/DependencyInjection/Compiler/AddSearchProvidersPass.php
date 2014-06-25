<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add search providers pass
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
        $searches = array();
        foreach ($container->findTaggedServiceIds('phlexible_search.provider') as $id => $definition) {
            $searches[] = new Reference($id);
        }
        $container->getDefinition('phlexible_search.search_providers')->replaceArgument(0, $searches);
    }
}
