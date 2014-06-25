<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle;

use Phlexible\Bundle\MediaAssetBundle\DependencyInjection\Compiler\AddAttributeReadersPass;
use Phlexible\Bundle\MediaAssetBundle\DependencyInjection\Compiler\AddMetaReadersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Media asset bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaAssetBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddAttributeReadersPass());
        $container->addCompilerPass(new AddMetaReadersPass());
    }
}
