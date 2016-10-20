<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle;

use Phlexible\Bundle\MediaCacheBundle\DependencyInjection\Compiler\AddWorkersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Media cache bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaCacheBundle extends Bundle
{
    const RESOURCE_MEDIA_CACHE = 'mediacache';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddWorkersPass());
    }
}
