<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
