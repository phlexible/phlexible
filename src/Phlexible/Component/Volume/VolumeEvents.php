<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume;

/**
 * Volume events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VolumeEvents
{
    /**
     * Called before an upload is stored
     */
    const BEFORE_STORE_UPLOAD = 'volume.before_store_upload';

    /**
     * Called after an upload has been stored
     */
    const STORE_UPLOAD = 'volume.store_upload';

    /**
     * Called before an upload is saved
     */
    const BEFORE_SAVE_UPLOAD = 'volume.before_save_upload';

    /**
     * Called after an upload has been saved
     */
    const SAVE_UPLOAD = 'volume.save_upload';

    /**
     * Called before a file is imported to a folder
     */
    const BEFORE_IMPORT_FILE = 'volume.before_import_file';

    /**
     * Called after a file has been imported to a folder
     */
    const IMPORT_FILE = 'volume.import_file';

    /**
     * Called before a file is created
     */
    const BEFORE_CREATE_FILE = 'volume.before_create_file';

    /**
     * Called after a file has been created
     */
    const CREATE_FILE = 'volume.create_file';

    /**
     * Called before a file is replaced
     */
    const BEFORE_REPLACE_FILE = 'volume.before_replace_file';

    /**
     * Called after a file has been replaced
     */
    const REPLACE_FILE = 'volume.replace_file';

    /**
     * Called before a file is deleted
     */
    const CHECK_DELETE_FILE = 'volume.check_delete_file';

    /**
     * Called before a file is deleted from a folder
     */
    const BEFORE_DELETE_FILE = 'volume.before_delete_file';

    /**
     * Called after a file has been deleted from a folder
     */
    const DELETE_FILE = 'volume.delete_file';

    /**
     * Called before a file is hidden in a folder
     */
    const BEFORE_HIDE_FILE = 'volume.before_hide_file';

    /**
     * Called after a file has been hidden in a folder
     */
    const HIDE_FILE = 'volume.hide_file';

    /**
     * Called before a file is shown in a folder
     */
    const BEFORE_SHOW_FILE = 'volume.before_show_file';

    /**
     * Called after a file has been shown in a folder
     */
    const SHOW_FILE = 'volume.show_file';

    /**
     * Called before a file is moved
     */
    const BEFORE_MOVE_FILE = 'volume.before_move_file';

    /**
     * Called after a file has been moved
     */
    const MOVE_FILE = 'volume.move_file';

    /**
     * Called before a file is moved
     */
    const BEFORE_COPY_FILE = 'volume.before_copy_file';

    /**
     * Called after a file has been moved
     */
    const COPY_FILE = 'volume.copy_file';

    /**
     * Called before a folder is created
     */
    const BEFORE_CREATE_FOLDER = 'volume.before_create_folder';

    /**
     * Called after a folder has been created
     */
    const CREATE_FOLDER = 'volume.create_folder';

    /**
     * Called before a folder is deleted
     */
    const CHECK_DELETE_FOLDER = 'volume.check_delete_folder';

    /**
     * Called before a folder is deleted
     */
    const BEFORE_DELETE_FOLDER = 'volume.before_delete_folder';

    /**
     * Called after a folder has been deleted
     */
    const DELETE_FOLDER = 'volume.delete_folder';

    /**
     * Called before a folder is moved
     */
    const BEFORE_MOVE_FOLDER = 'volume.before_move_folder';

    /**
     * Called after a folder has been moved
     */
    const MOVE_FOLDER = 'volume.move_folder';

    /**
     * Called before a folder is copied
     */
    const BEFORE_COPY_FOLDER = 'volume.before_copy_folder';

    /**
     * Called after a folder has been copied
     */
    const COPY_FOLDER = 'volume.copy_folder';

    /**
     * Called before a folder is renamed
     */
    const BEFORE_RENAME_FOLDER = 'volume.before_rename_folder';

    /**
     * Called after a folder has been renamed
     */
    const RENAME_FOLDER = 'volume.rename_folder';

    /**
     * Called before setting file attributes
     */
    const BEFORE_SET_FOLDER_ATTRIBUTES = 'volume.before_set_folder_attributes';

    /**
     * Called after setting file attributes
     */
    const SET_FOLDER_ATTRIBUTES = 'volume.set_folder_attributes';

    /**
     * Called before a file gets renamed
     */
    const BEFORE_RENAME_FILE = 'volume.before_rename_file';

    /**
     * Called after a file has been renamed
     */
    const RENAME_FILE = 'volume.rename_file';

    /**
     * Called before a file gets renamed
     */
    const BEFORE_SET_FILE_ATTRIBUTES = 'volume.before_set_file_attributes';

    /**
     * Called after a file has been renamed
     */
    const SET_FILE_ATTRIBUTES = 'volume.set_file_attributes';
}
