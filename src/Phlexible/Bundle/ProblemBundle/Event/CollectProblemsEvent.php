<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Event;

use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Symfony\Component\EventDispatcher\Event;

/**
 * Collect problems event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CollectProblemsEvent extends Event
{
    /**
     * @var Problem[]
     */
    private $problems = array();

    /**
     * @return Problem[]
     */
    public function getProblems()
    {
        return $this->problems;
    }

    /**
     * @param Problem $problem
     *
     * @return $this
     */
    public function addProblem(Problem $problem)
    {
        $this->problems[] = $problem;

        return $this;
    }
}
