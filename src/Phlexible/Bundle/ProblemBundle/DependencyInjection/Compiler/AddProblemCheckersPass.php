<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add problem checkers pass.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddProblemCheckersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $problemCheckers = [];
        foreach (array_keys($container->findTaggedServiceIds('phlexible_problem.checker')) as $id) {
            $problemCheckers[] = new Reference($id);
        }
        $container->getDefinition('phlexible_problem.problem_checkers')->replaceArgument(0, $problemCheckers);
    }
}
