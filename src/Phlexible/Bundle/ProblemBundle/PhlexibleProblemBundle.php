<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
