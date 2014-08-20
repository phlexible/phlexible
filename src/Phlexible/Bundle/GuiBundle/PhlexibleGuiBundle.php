<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle;

use Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler\AddCompressorsPass;
use Phlexible\Bundle\GuiBundle\DependencyInjection\Compiler\AddAssetProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Gui bundle
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
            ->addCompilerPass(new AddAssetProvidersPass())
            ->addCompilerPass(new AddCompressorsPass());
    }
}
