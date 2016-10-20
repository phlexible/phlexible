<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
