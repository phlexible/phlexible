<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle;

/**
 * Media manager events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerEvents
{
    /**
     * Get Slots Event
     * Fired when slots are gathered
     */
    const GET_SLOTS = 'phlexible_media_manager.get_slots';

    /**
     * Before Save Meta Event
     * Fired before meta information are saved
     */
    const BEFORE_SAVE_META = 'phlexible_media_manager.before_save_meta';

    /**
     * Save Meta Event
     * Fired when meta information are saved
     */
    const SAVE_META = 'phlexible_media_manager.save_meta';

    /**
     * Before Save Folder Meta Event
     * Fired before meta information are saved
     */
    const BEFORE_SAVE_FOLDER_META = 'phlexible_media_manager.before_save_folder_meta';

    /**
     * Save Folder Meta Event
     * Fired when meta information are saved
     */
    const SAVE_FOLDER_META = 'phlexible_media_manager.save_folder_meta';
}
