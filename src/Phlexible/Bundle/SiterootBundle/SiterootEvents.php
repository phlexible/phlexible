<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle;

/**
 * Siteroot events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootEvents
{
    /**
     * Fired before a siteroot is created.
     */
    const BEFORE_CREATE_SITEROOT = 'phlexible_siteroot.before_create_siteroot';

    /**
     * Fired after a siteroot has been created.
     */
    const CREATE_SITEROOT = 'phlexible_siteroot.create_siteroot';

    /**
     * Fired before a siteroot is updates.
     */
    const BEFORE_UPDATE_SITEROOT = 'phlexible_siteroot.before_update_siteroot';

    /**
     * Fired after a siteroot has been updated.
     */
    const UPDATE_SITEROOT = 'phlexible_siteroot.update_siteroot';

    /**
     * Fired before a siteroot is saved.
     */
    const BEFORE_SAVE_SITEROOT = 'phlexible_siteroot.before_save_siteroot';

    /**
     * Fired after a siteroot has been saved.
     */
    const SAVE_SITEROOT = 'phlexible_siteroot.save_siteroot';

    /**
     * Fired before a siteroot is deleted.
     */
    const BEFORE_DELETE_SITEROOT = 'phlexible_siteroot.before_delete_siteroot';

    /**
     * Fired after a siteroot has been deleted.
     */
    const DELETE_SITEROOT = 'phlexible_siteroot.delete_siteroot';
}
