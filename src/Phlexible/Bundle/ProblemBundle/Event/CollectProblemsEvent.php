<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    private $problems = [];

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
