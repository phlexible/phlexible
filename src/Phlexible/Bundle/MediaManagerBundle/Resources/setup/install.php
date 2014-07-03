<?php

use Phlexible\Bundle\GuiBundle\Util\Uuid;

$setup = array(
    'database' => array(
        array(
            'action' => 'createTable',
            'data'   => array(
                DB_PREFIX . 'mediamanager_folders'               => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id'),
                    ),
                    'definition' => array(
                        'id'             => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'site_id'        => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'parent_id'      => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'name'           => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true
                        ),
                        'path'           => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true
                        ),
                        'deleted'        => array(
                            'type' => 'boolean',
                        ),
                        'create_user_id' => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true
                        ),
                        'create_time'    => array(
                            'type'    => 'timestamp',
                            'notnull' => true
                        ),
                        'modify_user_id' => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true
                        ),
                        'modify_time'    => array(
                            'type' => 'timestamp',
                        )
                    )
                ),
                DB_PREFIX . 'mediamanager_folder_rights'         => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('folder_id', 'object_type', 'object_id'),
                    ),
                    'definition' => array(
                        'folder_id'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'object_type'    => array(
                            'type'   => 'string',
                            'length' => 20,
                            'fixed'  => true,
                        ),
                        'object_id'      => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true
                        ),
                        'folder_read'    => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'folder_create'  => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'folder_modify'  => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'folder_delete'  => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'folder_rights'  => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'file_read'      => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'file_create'    => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'file_modify'    => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'file_delete'    => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'file_download'  => array(
                            'type'    => 'boolean',
                            'default' => 0,
                        ),
                        'inherit'        => array(
                            'type' => 'boolean',
                        ),
                        'stop'           => array(
                            'type' => 'boolean',
                        ),
                        'create_user_id' => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true
                        ),
                        'create_time'    => array(
                            'type'    => 'timestamp',
                            'notnull' => true
                        ),
                        'modify_user_id' => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true
                        ),
                        'modify_time'    => array(
                            'type' => 'timestamp',
                        )
                    )
                ),
                DB_PREFIX . 'mediamanager_files'                 => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id', 'version'),
                    ),
                    'definition' => array(
                        'id'                => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'version'           => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 1
                        ),
                        'folder_id'         => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'parent_id'         => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'name'              => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true
                        ),
                        'mime_type'         => array(
                            'type'    => 'string',
                            'length'  => 100,
                            'notnull' => true
                        ),
                        'document_type_key' => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'asset_type'        => array(
                            'type'    => 'string',
                            'length'  => 20,
                            'notnull' => true
                        ),
                        'asset_class'       => array(
                            'type'    => 'string',
                            'length'  => 100,
                            'notnull' => true
                        ),
                        'hash'              => array(
                            'type'    => 'string',
                            'length'  => 32,
                            'fixed'   => true,
                            'notnull' => true
                        ),
                        'size'              => array(
                            'type'     => 'integer',
                            'length'   => 8,
                            'unsigned' => true,
                            'notnull'  => true
                        ),
                        'deleted'           => array(
                            'type'    => 'boolean',
                            'default' => 0
                        ),
                        'hidden'            => array(
                            'type'    => 'boolean',
                            'default' => 0
                        ),
                        'lock'              => array(
                            'type' => 'boolean',
                        ),
                        'lock_user_id'      => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'lock_time'         => array(
                            'type' => 'timestamp',
                        ),
                        'create_user_id'    => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true
                        ),
                        'create_time'       => array(
                            'type'    => 'timestamp',
                            'notnull' => true
                        ),
                        'modify_user_id'    => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'modify_time'       => array(
                            'type' => 'timestamp',
                        )
                    )
                ),
                DB_PREFIX . 'mediamanager_files_attributes'      => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('file_id', 'file_version', 'attribute_key'),
                    ),
                    'definition' => array(
                        'file_id'         => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'file_version'    => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 1,
                        ),
                        'attribute_key'   => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'attribute_value' => array(
                            'type' => 'clob'
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_downloads'       => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('file_id'),
                    ),
                    'definition' => array(
                        'file_id'       => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'cnt'           => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 0
                        ),
                        'last_download' => array(
                            'type' => 'timestamp'
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_metasets'        => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('file_id', 'file_version', 'set_id')
                    ),
                    'definition' => array(
                        'file_id'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'file_version' => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 1,
                        ),
                        'set_id'       => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'classname'    => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_metasets_items'  => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('file_id', 'file_version', 'set_id', 'meta_key'),
                    ),
                    'definition' => array(
                        'file_id'       => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'file_version'  => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 1,
                        ),
                        'set_id'        => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'meta_key'      => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'meta_value'    => array(
                            'type' => 'clob',
                        ),
                        'meta_value_de' => array(
                            'type' => 'clob',
                        ),
                        'meta_value_en' => array(
                            'type' => 'clob',
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_folder_metasets'       => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('folder_id', 'set_id')
                    ),
                    'definition' => array(
                        'folder_id' => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'set_id'    => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_folder_metasets_items' => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('folder_id', 'set_id', 'meta_key'),
                    ),
                    'definition' => array(
                        'folder_id'     => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'set_id'        => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'meta_key'      => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'meta_value'    => array(
                            'type' => 'clob',
                        ),
                        'meta_value_de' => array(
                            'type' => 'clob',
                        ),
                        'meta_value_en' => array(
                            'type' => 'clob',
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_usage'           => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                    ),
                    'definition' => array(
                        'file_id'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'file_version' => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 1,
                        ),
                        'usage_type'   => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'usage_id'     => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'status'       => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 0,
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_folders_usage'         => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                    ),
                    'definition' => array(
                        'folder_id'  => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'usage_type' => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'usage_id'   => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'status'     => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 0,
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_site'                  => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id')
                    ),
                    'definition' => array(
                        'id'              => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'key'             => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'storage_driver'  => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'storage_options' => array(
                            'type' => 'clob',
                        ),
                        'driver'          => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'root_dir'        => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'quota'           => array(
                            'type'    => 'integer',
                            'length'  => 8,
                            'notnull' => true,
                            'default' => 0,
                        ),
                        'create_time'     => array(
                            'type'    => 'timestamp',
                            'notnull' => true
                        ),
                        'create_uid'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                    )
                ),
            ),
        ),
        array(
            'action' => 'createIndex',
            'data'   => array(
                DB_PREFIX . 'mediamanager_folders'          => array(
                    'mediamanager_folders_idx_0' => array(
                        'type'   => 'unique',
                        'fields' => array(
                            'site_id',
                            'path',
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files'            => array(
                    'mediamanager_files_idx_1' => array(
                        'fields' => array(
                            'folder_id',
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_attributes' => array(
                    'mediamanager_files_attributes_idx_0' => array(
                        'fields' => array(
                            'file_id',
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_usage'      => array(
                    'mediamanager_files_usage_idx_0' => array(
                        'fields' => array(
                            'usage_id',
                            'usage_type',
                        ),
                    ),
                ),
                DB_PREFIX . 'mediamanager_folders_usage'    => array(
                    'mediamanager_folders_usage_idx_0' => array(
                        'fields' => array(
                            'usage_id',
                            'usage_type',
                        ),
                    ),
                ),
            ),
        ),
        array(
            'action' => 'createForeignKey',
            'data'   => array(
                DB_PREFIX . 'mediamanager_folders'               => array(
                    array(
                        'name'         => 'mediamanager_site_to_mediamanager_folders',
                        'local'        => 'site_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'mediamanager_site',
                        'onDelete'     => 'NO ACTION',
                        'onUpdate'     => 'NO ACTION'
                    ),
                    array(
                        'name'         => 'mediamanager_folders_to_mediamanager_folders',
                        'local'        => 'parent_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'mediamanager_folders',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_folder_rights'         => array(
                    array(
                        'name'         => 'mediamanager_folder_to_mediamanager_folder_rights',
                        'local'        => 'folder_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'mediamanager_folders',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_files'                 => array(
                    array(
                        'name'         => 'mediamanager_folders_to_mediamanager_files',
                        'local'        => 'folder_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'mediamanager_folders',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_attributes'      => array(
                    array(
                        'name'         => 'mediamanager_files_to_mediamanager_files_attributes',
                        'local'        => array('file_id', 'file_version'),
                        'foreign'      => array('id', 'version'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_files',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_downloads'       => array(
                    array(
                        'name'         => 'mediamanager_files_to_mediamanager_files_downloads',
                        'local'        => array('file_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_files',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_metasets'        => array(
                    array(
                        'name'         => 'mf_to_mfm',
                        'local'        => array('file_id', 'file_version'),
                        'foreign'      => array('id', 'version'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_files',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_metasets_items'  => array(
                    array(
                        'name'         => 'mfm_to_mfmi',
                        'local'        => array('file_id', 'file_version', 'set_id'),
                        'foreign'      => array('file_id', 'file_version', 'set_id'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_files_metasets',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_folder_metasets'       => array(
                    array(
                        'name'         => 'mfo_to_mfom',
                        'local'        => array('folder_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_folders',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_folder_metasets_items' => array(
                    array(
                        'name'         => 'mfom_to_mfomi',
                        'local'        => array('folder_id', 'set_id'),
                        'foreign'      => array('folder_id', 'set_id'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_folder_metasets',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_files_usage'           => array(
                    array(
                        'name'         => 'mf_to_mfu',
                        'local'        => array('file_id', 'file_version'),
                        'foreign'      => array('id', 'version'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_files',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
                DB_PREFIX . 'mediamanager_folders_usage'         => array(
                    array(
                        'name'         => 'mfo_to_mfou',
                        'local'        => array('folder_id'),
                        'foreign'      => array('id',),
                        'foreignTable' => DB_PREFIX . 'mediamanager_folders',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
            ),
        ),
        array(
            'action' => 'insertData',
            'data'   => array(
                DB_PREFIX . 'mediamanager_site'          => array(
                    array(
                        'id'          => '492d88c5-dd48-4d73-b849-1cacc0a80056',
                        'key'         => 'mediamanager',
                        'driver'      => 'Media_SiteDb_Site',
                        'root_dir'    => /*DATA_DIR . */
                            'mediamanager/',
                        'quota'       => 10000000000,
                        'create_time' => $db->fn->now(),
                        'create_uid'  => SYSTEM_UID,
                    )
                ),
                DB_PREFIX . 'mediamanager_folders'       => array(
                    array(
                        'id'             => $rootID = Uuid::generate(),
                        'parent_id'      => null,
                        'site_id'        => '492d88c5-dd48-4d73-b849-1cacc0a80056',
                        'name'           => 'Root',
                        'path'           => '',
                        'create_user_id' => SYSTEM_UID,
                        'create_time'    => $db->fn->now()
                    ),
                ),
                DB_PREFIX . 'mediamanager_folder_rights' => array(
                    array(
                        'folder_id'      => $rootID,
                        'object_type'    => 'group',
                        'object_id'      => $db = MWF_Registry::getContainer()->dbPool->default->fetchOne(
                                $db = MWF_Registry::getContainer()->dbPool->default->select()->from(
                                    DB_PREFIX . 'group',
                                    'gid'
                                )->where('name = ?', 'everyone')->limit(1)
                            ),
                        'folder_read'    => 1,
                        'folder_create'  => 1,
                        'folder_modify'  => 1,
                        'folder_delete'  => 1,
                        'file_read'      => 1,
                        'file_create'    => 1,
                        'file_modify'    => 1,
                        'file_delete'    => 1,
                        'create_user_id' => SYSTEM_UID,
                        'create_time'    => $db->fn->now()
                    ),
                ),
            ),
        ),
    ),
);
