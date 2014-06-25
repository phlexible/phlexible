<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle;

/**
 * Siteroot events
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
interface SiterootEvents
{
    /**
     * Before Create Siteroot Event
     * Fired before a siteroot is created.
     */
    const BEFORE_CREATE_SITEROOT = 'phlexible_siteroot.before_create_siteroot';

    /**
     * Create Siteroot Event
     * Fired after a siteroot has been created.
     */
    const CREATE_SITEROOT = 'phlexible_siteroot.create_siteroot';

    /**
     * Before Save Siteroot Event
     * Fired before a siteroot is saved.
     */
    const BEFORE_SAVE_SITEROOT = 'phlexible_siteroot.before_save_siteroot';

    /**
     * Save Siteroot Event
     * Fired after a siteroot has been saved.
     */
    const SAVE_SITEROOT = 'phlexible_siteroot.save_siteroot';

    /**
     * Before Delete Siteroot Event
     * Fired before a siteroot is deleted.
     */
    const BEFORE_DELETE_SITEROOT = 'phlexible_siteroot.before_delete_siteroot';

    /**
     * Delete Siteroot Event
     * Fired after a siteroot has been deleted.
     */
    const DELETE_SITEROOT = 'phlexible_siteroot.delete_siteroot';
}
