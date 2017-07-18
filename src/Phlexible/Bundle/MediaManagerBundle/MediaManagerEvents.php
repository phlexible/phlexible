<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle;

/**
 * Media manager events.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerEvents
{
    /**
     * Fired when slots are gathered.
     */
    const GET_SLOTS = 'phlexible_media_manager.get_slots';

    /**
     * Fired before meta information are saved.
     */
    const BEFORE_SAVE_META = 'phlexible_media_manager.before_save_meta';

    /**
     * Fired when meta information are saved.
     */
    const SAVE_META = 'phlexible_media_manager.save_meta';

    /**
     * Fired before meta information are saved.
     */
    const BEFORE_SAVE_FOLDER_META = 'phlexible_media_manager.before_save_folder_meta';

    /**
     * Fired when meta information are saved.
     */
    const SAVE_FOLDER_META = 'phlexible_media_manager.save_folder_meta';

    const BEFORE_SET_FOLDER_METASETS = 'phlexible_media_manager.before_set_folder_metasets';
    const SET_FOLDER_METASETS = 'phlexible_media_manager.set_folder_metasets';
    const BEFORE_SET_FILE_METASETS = 'phlexible_media_manager.before_set_file_metasets';
    const SET_FILE_METASETS = 'phlexible_media_manager.set_file_metasets';
    const BEFORE_SET_FILE_MEDIA_TYPE = 'phlexible_media_manager.before_set_file_media_type';
    const SET_FILE_MEDIA_TYPE = 'phlexible_media_manager.set_file_media_type';
    const CHECK_FILE_UPLOAD = 'phlexible_media_manager.check_file_upload';
}
