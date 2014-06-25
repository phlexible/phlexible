<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add database connections pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddDatabaseConnectionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $dbPoolDefinition = $container->findDefinition('connection_manager');
        foreach ($container->findTaggedServiceIds('database.connection') as $id => $attributes) {
            $connectionName = strtolower(substr($id, 20));
            $dbPoolDefinition->addMethodCall(
                'set',
                array($connectionName, new Reference($id))
            );
        }
    }
}
