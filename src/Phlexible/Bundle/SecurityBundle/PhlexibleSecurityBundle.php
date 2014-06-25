<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle;

use Phlexible\Bundle\SecurityBundle\DependencyInjection\Compiler\AddAclProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Security bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleSecurityBundle extends Bundle
{
    const RESOURCE_ROLES = 'roles';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddAclProvidersPass());
    }
}
