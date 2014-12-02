<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
