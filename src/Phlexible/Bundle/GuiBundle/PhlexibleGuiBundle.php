<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle;

use Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler\AddCompressorsPass;
use Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler\AddHelperAssetsPass;
use Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler\AddRoleProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Gui bundle.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleGuiBundle extends Bundle
{
    const RESOURCE_EXTENSIONS = 'extensions';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new AddHelperAssetsPass())
            ->addCompilerPass(new AddCompressorsPass())
            ->addCompilerPass(new AddRoleProvidersPass());
    }
}
