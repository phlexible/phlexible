<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add portlets pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddPortletsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $portlets = array();
        foreach ($container->findTaggedServiceIds('phlexible_dashboard.portlet') as $id => $attributes) {
            $portlets[] = new Reference($id);
        }
        $container->getDefinition('phlexible_dashboard.portlets')->replaceArgument(0, $portlets);
    }
}
