<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\ProblemChecker;

/**
 * Problem checker interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ProblemCheckerInterface
{
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_WARNING  = 'warning';
    const SEVERITY_INFO     = 'info';

    /**
     * Check for problems
     *
     * @return mixed
     */
    public function check();
}
