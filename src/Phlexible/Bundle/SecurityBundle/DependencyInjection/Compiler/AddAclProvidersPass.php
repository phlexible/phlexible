<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add ACL providers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddAclProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $providers = array();
        foreach ($container->findTaggedServiceIds('phlexible_security.acl.provider') as $id => $definition) {
            $providers[] = new Reference($id);
        }
        $container->getDefinition('phlexible_security.acl.acl_provider')->replaceArgument(0, $providers);
    }
}
