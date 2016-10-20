<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\ProblemChecker;

/**
 * Problem checker collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemCheckerCollection implements \IteratorAggregate
{
    /**
     * @var ProblemCheckerInterface[]
     */
    private $problemCheckers = array();

    /**
     * @param ProblemCheckerInterface[] $problemCheckers
     */
    public function __construct(array $problemCheckers = array())
    {
        foreach ($problemCheckers as $problemChecker) {
            $this->addProblemChecker($problemChecker);
        }
    }

    /**
     * @param ProblemCheckerInterface $problemChecker
     *
     * @return $this
     */
    public function addProblemChecker(ProblemCheckerInterface $problemChecker)
    {
        $this->problemCheckers[] = $problemChecker;

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->problemCheckers);
    }
}
