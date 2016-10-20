<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
