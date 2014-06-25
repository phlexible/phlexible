<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle;

use Phlexible\Bundle\DashboardBundle\DependencyInjection\Compiler\AddPortletsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Dashboard bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleDashboardBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new AddPortletsPass())
        ;
    }
}
