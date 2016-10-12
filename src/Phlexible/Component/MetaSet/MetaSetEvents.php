<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet;

/**
 * Meta set events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetEvents
{
    /**
     * Fired before a meta data value is updated
     */
    const BEFORE_UPDATE_META_DATA_VALUE = 'phlexible_meta_set.before_update_meta_data_value';

    /**
     * Fired after a meta data value is updated
     */
    const UPDATE_META_DATA_VALUE = 'phlexible_meta_set.update_meta_data_value';
}
