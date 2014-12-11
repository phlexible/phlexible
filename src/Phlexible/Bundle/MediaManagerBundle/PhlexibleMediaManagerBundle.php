<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle;

use Phlexible\Bundle\MediaManagerBundle\DependencyInjection\Compiler\AddAttributeReadersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Media manager bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaManagerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddAttributeReadersPass());
    }
}
