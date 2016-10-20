<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementRendererBundle;

use Phlexible\Bundle\ElementRendererBundle\DependencyInjection\Compiler\AddConfiguratorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Element renderer component
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementRendererBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddConfiguratorsPass());
    }
}
