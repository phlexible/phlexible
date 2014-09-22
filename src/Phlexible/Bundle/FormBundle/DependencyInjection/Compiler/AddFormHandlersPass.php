<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add form handlers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFormHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $permissions = $container->findDefinition('phlexible_form.form_handlers');
        foreach ($container->findTaggedServiceIds('phlexible_form.form_handler') as $id => $attributes) {
            $permissions->addMethodCall('addFormHandler', array(new Reference($id)));
        }
    }
}
