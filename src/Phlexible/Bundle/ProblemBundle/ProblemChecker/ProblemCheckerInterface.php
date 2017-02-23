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
 * Problem checker interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ProblemCheckerInterface
{
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_INFO = 'info';

    /**
     * Check for problems.
     *
     * @return mixed
     */
    public function check();
}
