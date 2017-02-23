<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\ElementtypeBundle\DependencyInjection\Compiler\AddFieldsPass;
use Phlexible\Bundle\ElementtypeBundle\DependencyInjection\Compiler\AddSelectFieldProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Elementtype bundle.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementtypeBundle extends Bundle
{
    const RESOURCE_ELEMENT_TYPES = 'elementtypes';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddFieldsPass());
        $container->addCompilerPass(new AddSelectFieldProvidersPass());
    }
}
