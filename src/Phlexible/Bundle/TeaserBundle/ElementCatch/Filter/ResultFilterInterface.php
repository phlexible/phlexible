<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch\Filter;

use Phlexible\Bundle\TeaserBundle\ElementCatch\ElementCatchResultPool;

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
     * @param ElementCatchResultPool $result
     */
    public function filterResult(ElementCatchResultPool $result);

    /**
     * Returns true if filter is used in this request.
     *
     * @return bool
     */
    public function isActive();
}
