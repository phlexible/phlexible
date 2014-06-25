<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\ElementtypeBundle\DependencyInjection\Compiler\AddFieldsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Elementtype bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementtypeBundle extends Bundle
{
    const RESOURCE_ELEMENT_TYPES = 'elementtypes';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddFieldsPass());
    }
}
