<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle;

use Phlexible\Bundle\SearchBundle\DependencyInjection\Compiler\AddSearchProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Search bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleSearchBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddSearchProvidersPass());
    }
}
