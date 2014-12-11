<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle;

use Phlexible\Bundle\MediaExtractorBundle\DependencyInjection\Compiler\AddExtractorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Media extractor bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaExtractorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddExtractorsPass());
    }
}
