<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add field mappers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFieldMappersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $mappers = [];
        foreach ($container->findTaggedServiceIds('phlexible_element.field_mapper') as $id => $attributes) {
            $mappers[] = new Reference($id);
        }
        $mapper = $container->findDefinition('phlexible_element.field_mapper');
        $mapper->replaceArgument(2, $mappers);
    }
}
