<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle;

/**
 * Data source events
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class DataSourceEvents
{
    /**
     * Fired before garbage collection is invoked
     */
    const BEFORE_GARBAGE_COLLECT = 'phlexible_data_source.before_garbage_collect';

    /**
     * Fired after garbage collection is invoked
     */
    const GARBAGE_COLLECT = 'phlexible_data_source.garbage_collect';
}
