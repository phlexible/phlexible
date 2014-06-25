<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add problem checkers pass
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddProblemCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $problemCheckers = array();
        foreach (array_keys($container->findTaggedServiceIds('phlexible_problem.checker')) as $id) {
            $problemCheckers[] = new Reference($id);
        }
        $container->getDefinition('phlexible_problem.problem_checkers')->replaceArgument(0, $problemCheckers);
    }
}
