<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\ProblemChecker;

use Phlexible\Bundle\CacheBundle\Cache\CacheCollection;
use Phlexible\Bundle\ProblemBundle\ProblemChecker\ProblemCheckerInterface;

/**
 * Connection check
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ConnectionChecker implements ProblemCheckerInterface
{
    protected $severity = self::SEVERITY_WARNING;

    /**
     * @var CacheCollection
     */
    protected $caches = null;

    /**
     * @param CacheCollection $caches
     */
    public function __construct(CacheCollection $caches)
    {
        $this->caches = $caches;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $problems = array();

        return $problems;
    }
}