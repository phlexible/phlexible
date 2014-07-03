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
     * Fired before values are marked inactive
     */
    const BEFORE_MARK_ACTIVE = 'phlexible_data_source.before_mark_active';

    /**
     * Fired after values are marked active
     */
    const MARK_ACTIVE = 'phlexible_data_source.mark_active';

    /**
     * Fired before values are marked inactive
     */
    const BEFORE_MARK_INACTIVE = 'phlexible_data_source.before_mark_inactive';

    /**
     * Fired after values are marked inactive
     */
    const MARK_INACTIVE = 'phlexible_data_source.mark_inactive';

    /**
     * Fired before values are deleted
     */
    const BEFORE_DELETE_VALUES = 'phlexible_data_source.before_delete_values';

    /**
     * Fired after values are deleted
     */
    const DELETE_VALUES = 'phlexible_data_source.delete_values';
}
