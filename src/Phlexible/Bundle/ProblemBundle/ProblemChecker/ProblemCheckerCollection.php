<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
