<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle;

/**
 * Site events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaSiteEvents
{
    /**
     * Called before an upload is stored
     */
    const BEFORE_STORE_UPLOAD = 'phlexible_media_site.before_store_upload';

    /**
     * Called after an upload has been stored
     */
    const STORE_UPLOAD = 'phlexible_media_site.store_upload';

    /**
     * Called before an upload is saved
     */
    const BEFORE_SAVE_UPLOAD = 'phlexible_media_site.before_save_upload';

    /**
     * Called after an upload has been saved
     */
    const SAVE_UPLOAD = 'phlexible_media_site.save_upload';

    /**
     * Called before a file is imported to a folder
     */
    const BEFORE_IMPORT_FILE = 'phlexible_media_site.before_import_file';

    /**
     * Called after a file has been imported to a folder
     */
    const IMPORT_FILE = 'phlexible_media_site.import_file';

    /**
     * Called before a file is created
     */
    const BEFORE_CREATE_FILE = 'phlexible_media_site.before_create_file';

    /**
     * Called after a file has been created
     */
    const CREATE_FILE = 'phlexible_media_site.create_file';

    /**
     * Called before a file is replaced
     */
    const BEFORE_REPLACE_FILE = 'phlexible_media_site.before_replace_file';

    /**
     * Called after a file has been replaced
     */
    const REPLACE_FILE = 'phlexible_media_site.replace_file';

    /**
     * Called before a file is deleted
     */
    const CHECK_DELETE_FILE = 'phlexible_media_site.check_delete_file';

    /**
     * Called before a file is deleted from a folder
     */
    const BEFORE_DELETE_FILE = 'phlexible_media_site.before_delete_file';

    /**
     * Called after a file has been deleted from a folder
     */
    const DELETE_FILE = 'phlexible_media_site.delete_file';

    /**
     * Called before a file is hidden in a folder
     */
    const BEFORE_HIDE_FILE = 'phlexible_media_site.before_hide_file';

    /**
     * Called after a file has been hidden in a folder
     */
    const HIDE_FILE = 'phlexible_media_site.hide_file';

    /**
     * Called before a file is shown in a folder
     */
    const BEFORE_SHOW_FILE = 'phlexible_media_site.before_show_file';

    /**
     * Called after a file has been shown in a folder
     */
    const SHOW_FILE = 'phlexible_media_site.show_file';

    /**
     * Called before a file is moved
     */
    const BEFORE_MOVE_FILE = 'phlexible_media_site.before_move_file';

    /**
     * Called after a file has been moved
     */
    const MOVE_FILE = 'phlexible_media_site.move_file';

    /**
     * Called before a file is moved
     */
    const BEFORE_COPY_FILE = 'phlexible_media_site.before_copy_file';

    /**
     * Called after a file has been moved
     */
    const COPY_FILE = 'phlexible_media_site.copy_file';

    /**
     * Called before a folder is created
     */
    const BEFORE_CREATE_FOLDER = 'phlexible_media_site.before_create_folder';

    /**
     * Called after a folder has been created
     */
    const CREATE_FOLDER = 'phlexible_media_site.create_folder';

    /**
     * Called before a folder is deleted
     */
    const CHECK_DELETE_FOLDER = 'phlexible_media_site.check_delete_folder';

    /**
     * Called before a folder is deleted
     */
    const BEFORE_DELETE_FOLDER = 'phlexible_media_site.before_delete_folder';

    /**
     * Called after a folder has been deleted
     */
    const DELETE_FOLDER = 'phlexible_media_site.delete_folder';

    /**
     * Called before a folder is moved
     */
    const BEFORE_MOVE_FOLDER = 'phlexible_media_site.before_move_folder';

    /**
     * Called after a folder has been moved
     */
    const MOVE_FOLDER = 'phlexible_media_site.move_folder';

    /**
     * Called before a folder is copied
     */
    const BEFORE_COPY_FOLDER = 'phlexible_media_site.before_copy_folder';

    /**
     * Called after a folder has been copied
     */
    const COPY_FOLDER = 'phlexible_media_site.copy_folder';

    /**
     * Called before a folder is renamed
     */
    const BEFORE_RENAME_FOLDER = 'phlexible_media_site.before_rename_folder';

    /**
     * Called after a folder has been renamed
     */
    const RENAME_FOLDER = 'phlexible_media_site.rename_folder';

    /**
     * Called before setting file attributes
     */
    const BEFORE_SET_FOLDER_ATTRIBUTES = 'phlexible_media_site.before_set_folder_attributes';

    /**
     * Called after setting file attributes
     */
    const SET_FOLDER_ATTRIBUTES = 'phlexible_media_site.set_folder_attributes';

    /**
     * Called before a file gets renamed
     */
    const BEFORE_RENAME_FILE = 'phlexible_media_site.before_rename_file';

    /**
     * Called after a file has been renamed
     */
    const RENAME_FILE = 'phlexible_media_site.rename_file';

    /**
     * Called before a file gets renamed
     */
    const BEFORE_SET_FILE_ATTRIBUTES = 'phlexible_media_site.before_set_file_attributes';

    /**
     * Called after a file has been renamed
     */
    const SET_FILE_ATTRIBUTES = 'phlexible_media_site.set_file_attributes';
}
