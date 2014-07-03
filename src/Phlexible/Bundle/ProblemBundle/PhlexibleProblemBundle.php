<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle;

use Phlexible\Bundle\ProblemBundle\DependencyInjection\Compiler\AddProblemCheckersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Problem bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleProblemBundle extends Bundle
{
    const RESOURCE_PROBLEMS = 'problems';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddProblemCheckersPass());
    }
}
