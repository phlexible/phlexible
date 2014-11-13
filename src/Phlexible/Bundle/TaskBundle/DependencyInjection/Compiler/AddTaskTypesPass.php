<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add task types pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddTaskTypesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $types = [];
        foreach ($container->findTaggedServiceIds('phlexible_task.type') as $id => $definition) {
            $types[] = new Reference($id);
        }

        $container->findDefinition('phlexible_task.types')->replaceArgument(0, $types);
    }
}
