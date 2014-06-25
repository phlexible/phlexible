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
interface MediaSiteEvents
{
    /**
     * Before Store Upload Event
     * Called before an upload is stored
     */
    const BEFORE_STORE_UPLOAD = 'phlexible_media_site.before_store_upload';

    /**
     * Store Upload Event
     * Called after an upload has been stored
     */
    const STORE_UPLOAD = 'phlexible_media_site.store_upload';

    /**
     * Before Save Upload Event
     * Called before an upload is saved
     */
    const BEFORE_SAVE_UPLOAD = 'phlexible_media_site.before_save_upload';

    /**
     * Save Upload Event
     * Called after an upload has been saved
     */
    const SAVE_UPLOAD = 'phlexible_media_site.save_upload';

    /**
     * Before Import File Event
     * Called before a file is imported to a folder
     */
    const BEFORE_IMPORT_FILE = 'phlexible_media_site.before_import_file';

    /**
     * Import File Event
     * Called after a file has been imported to a folder
     */
    const IMPORT_FILE = 'phlexible_media_site.import_file';

    /**
     * Before Create File Event
     * Called before a file is created
     */
    const BEFORE_CREATE_FILE = 'phlexible_media_site.before_create_file';

    /**
     * Create File Event
     * Called after a file has been created
     */
    const CREATE_FILE = 'phlexible_media_site.create_file';

    /**
     * Before Delete File Event
     * Called before a file is deleted from a folder
     */
    const BEFORE_DELETE_FILE = 'phlexible_media_site.before_delete_file';

    /**
     * Delete File Event
     * Called after a file has been deleted from a folder
     */
    const DELETE_FILE = 'phlexible_media_site.delete_file';

    /**
     * Before Hide File Event
     * Called before a file is hidden in a folder
     */
    const BEFORE_HIDE_FILE = 'phlexible_media_site.before_hide_file';

    /**
     * Hide File Event
     * Called after a file has been hidden in a folder
     */
    const HIDE_FILE = 'phlexible_media_site.hide_file';

    /**
     * Before Show File Event
     * Called before a file is shown in a folder
     */
    const BEFORE_SHOW_FILE = 'phlexible_media_site.before_show_file';

    /**
     * Show File Event
     * Called after a file has been shown in a folder
     */
    const SHOW_FILE = 'phlexible_media_site.show_file';

    /**
     * Before Move File Event
     * Called before a file is moved
     */
    const BEFORE_MOVE_FILE = 'phlexible_media_site.before_move_file';

    /**
     * Add Move Event
     * Called after a file has been moved
     */
    const MOVE_FILE = 'phlexible_media_site.move_file';

    /**
     * Before Create Folder Event
     * Called before a folder is created
     */
    const BEFORE_CREATE_FOLDER = 'phlexible_media_site.before_create_folder';

    /**
     * Create Folder Event
     * Called after a folder has been created
     */
    const CREATE_FOLDER = 'phlexible_media_site.create_folder';

    /**
     * Before Create Folder Event
     * Called before a folder is deleted
     */
    const BEFORE_DELETE_FOLDER = 'phlexible_media_site.before_delete_folder';

    /**
     * Create Folder Event
     * Called after a folder has been deleted
     */
    const DELETE_FOLDER = 'phlexible_media_site.delete_folder';

    /**
     * Before Move Folder Event
     * Called before a folder is moved
     */
    const BEFORE_MOVE_FOLDER = 'phlexible_media_site.before_move_folder';

    /**
     * Move Folder Event
     * Called after a folder has been moved
     */
    const MOVE_FOLDER = 'phlexible_media_site.move_folder';

    /**
     * Rename Folder Event
     * Called before a folder gets renamed
     */
    const BEFORE_RENAME_FOLDER = 'phlexible_media_site.before_rename_folder';

    /**
     * Rename Folder Event
     * Called after a folder has been renamed
     */
    const RENAME_FOLDER = 'phlexible_media_site.rename_folder';

    /**
     * Before Set Folder Attributes Event
     */
    const BEFORE_SET_FOLDER_ATTRIBUTES = 'phlexible_media_site.before_set_folder_attributes';

    /**
     * Set File Attributes Event
     */
    const SET_FOLDER_ATTRIBUTES = 'phlexible_media_site.set_folder_attributes';

    /**
     * Before Replace File Event
     * Called before a file is replaced
     */
    const BEFORE_REPLACE_FILE = 'phlexible_media_site.before_replace_file';

    /**
     * Replace Folder Event
     * Called after a folder has been moved
     */
    const REPLACE_FILE = 'phlexible_media_site.replace_file';

    /**
     * Before Rename File Event
     * Called before a file gets renamed
     */
    const BEFORE_RENAME_FILE = 'phlexible_media_site.before_rename_file';

    /**
     * Rename File Event
     * Called after a file has been renamed
     */
    const RENAME_FILE = 'phlexible_media_site.rename_file';

    /**
     * Before Set File Attributes Event
     * Called before a file gets renamed
     */
    const BEFORE_SET_FILE_ATTRIBUTES = 'phlexible_media_site.before_set_file_attributes';

    /**
     * Set File Attributes Event
     * Called after a file has been renamed
     */
    const SET_FILE_ATTRIBUTES = 'phlexible_media_site.set_file_attributes';
}
