<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Add fields pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFieldsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $fields = [];
        foreach ($container->findTaggedServiceIds('phlexible_elementtype.field') as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException('Tag phlexible_elementtype.field needs alias');
            }

            $definition = $container->findDefinition($id);
            $fields[$attributes[0]['alias']] = $definition->getClass();
        }
        $container->findDefinition('phlexible_elementtype.field.registry')->replaceArgument(0, $fields);
    }
}
