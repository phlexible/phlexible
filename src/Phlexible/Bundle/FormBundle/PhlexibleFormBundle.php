<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle;

use Phlexible\Bundle\FormBundle\DependencyInjection\Compiler\AddFormHandlersPass;
use Phlexible\Bundle\ProblemBundle\Problem;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Form bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddFormHandlersPass());
    }
}
