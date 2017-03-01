<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle;

use Phlexible\Bundle\DashboardBundle\DependencyInjection\Compiler\AddPortletsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Dashboard bundle.
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
            ->addCompilerPass(new AddPortletsPass());
    }
}
