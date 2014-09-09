<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinderResultPool;

/**
 * Result filter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ResultFilterInterface
{
    /**
     * Function to apply some postprocesing to the result.
     *
     * @param ElementFinderResultPool $result
     */
    public function filterResult(ElementFinderResultPool $result);

    /**
     * Returns true if filter is used in this request.
     *
     * @return bool
     */
    public function isActive();
}
