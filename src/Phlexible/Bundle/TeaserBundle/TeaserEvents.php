<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle;

/**
 * Teaser events.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserEvents
{
    /**
     * Fired before a teaser is created.
     */
    const BEFORE_CREATE_TEASER = 'phlexible_teaser.before_create_teaser';

    /**
     * Fired after a teaser has been created.
     */
    const CREATE_TEASER = 'phlexible_teaser.create_teaser';

    /**
     * Fired before a teaser instance is created.
     */
    const BEFORE_CREATE_TEASER_INSTANCE = 'phlexible_teaser.before_create_teaser_instance';

    /**
     * Fired after a teaser instance has been created.
     */
    const CREATE_TEASER_INSTANCE = 'phlexible_teaser.create_teaser_instance';

    /**
     * Fired before a teaser is deleted.
     */
    const BEFORE_DELETE_TEASER = 'phlexible_teaser.before_delete_teaser';

    /**
     * Fired after a teaser has been deleted.
     */
    const DELETE_TEASER = 'phlexible_teaser.delete_teaser';

    /**
     * Fired before a teaser is updated.
     */
    const BEFORE_UPDATE_TEASER = 'phlexible_teasers.before_update_teaser';

    /**
     * Fired after a teaser has been updated.
     */
    const UPDATE_TEASER = 'phlexible_teaser.update_teaser';

    /**
     * Fired before a teaser is published.
     */
    const BEFORE_PUBLISH_TEASER = 'phlexible_teaser.before_publish_teaser';

    /**
     * Fired after a teaser has been published.
     */
    const PUBLISH_TEASER = 'phlexible_teaser.publish_teaser';

    /**
     * Fired before a teaser is set offline.
     */
    const BEFORE_SET_TEASER_OFFLINE = 'phlexible_teaser.before_set_teaser_offline';

    /**
     * Fired after a teaser has been set offline.
     */
    const SET_TEASER_OFFLINE = 'phlexible_teaser.set_teaser_offline';

    /**
     * Fired before a teaser is shown.
     */
    const BEFORE_SHOW_TEASER = 'phlexible_teaser.before_show_teaser';

    /**
     * Fired after a teaser has been shown.
     */
    const SHOW_TEASER = 'phlexible_teaser.show_teaser';

    /**
     * Fired before a teaser is hidden.
     */
    const BEFORE_HIDE_TEASER = 'phlexible_teaser.before_hide_teaser';

    /**
     * Fired after a teaser has been hidden.
     */
    const HIDE_TEASER = 'phlexible_teaser.hide_teaser';

    /**
     * Fired before a teaser is inherited.
     */
    const BEFORE_INHERIT_TEASER = 'phlexible_teaser.before_inherit_teaser';

    /**
     * Fired after a teaser has been inherited.
     */
    const INHERIT_TEASER = 'phlexible_teaser.inherit_teaser';

    /**
     * Fired before a teaser is stopped from inherit.
     */
    const BEFORE_STOP_TEASER = 'phlexible_teaser.before_stop_teaser';

    /**
     * Fired after a teaser has been stopped.
     */
    const STOP_TEASER = 'phlexible_teaser.stop_teaser';

    /**
     * Fired before teasers are reordered.
     */
    const BEFORE_REORDER_TEASERS = 'phlexible_teaser.before_reorder_teasers';

    /**
     * Fired after teasers have been reordered.
     */
    const REORDER_TEASERS = 'phlexible_teaser.reorder_teasers';
}
