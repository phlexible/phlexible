<?php

$setup = array(
    'database' => array(
        array(
            'action' => 'createTable',
            'data'   => array(
                DB_PREFIX . 'element'                       => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('eid'),
                    ),
                    'definition' => array(
                        'eid'             => array(
                            'type'          => 'integer',
                            'notnull'       => true,
                            'unsigned'      => true,
                            'autoincrement' => true,
                        ),
                        'unique_id'       => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'element_type_id' => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'create_uid'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'create_time'     => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'modify_uid'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'modify_time'     => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'latest_version'  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            'default'  => 1
                        ),
                        'masterlanguage'  => array(
                            'type'    => 'string',
                            'length'  => 2,
                            'notnull' => true,
                            'default' => 'de'
                        ),
                    )
                ),
                DB_PREFIX . 'element_data'                  => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('eid', 'version', 'data_id'),
                    ),
                    'definition' => array(
                        'eid'              => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'version'          => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'data_id'          => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            #'autoincrement' => true,
                        ),
                        'parent_id'        => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'parent_ds_id'     => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'ds_id'            => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'title'            => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'cnt'              => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'repeatable_node'  => array(
                            'type' => 'boolean',
                        ),
                        'repeatable_id'    => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'repeatable_ds_id' => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'sort'             => array(
                            'type' => 'integer',
                        ),
                        'content_channels' => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => false,
                        ),
                    )
                ),
                DB_PREFIX . 'element_data_language'         => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('eid', 'version', 'data_id', 'language')
                    ),
                    'definition' => array(
                        'data_id'  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language' => array(
                            'type'    => 'string',
                            'length'  => 2,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'content'  => array(
                            'type' => 'clob',
                        ),
                        'options'  => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'eid'      => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'version'  => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                    )
                ),
                DB_PREFIX . 'element_history'               => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id'),
                    ),
                    'definition' => array(
                        'id'          => array(
                            'type'          => 'integer',
                            'notnull'       => true,
                            'unsigned'      => true,
                            'autoincrement' => true,
                        ),
                        'eid'         => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language'    => array(
                            'type'   => 'string',
                            'length' => 2,
                            'fixed'  => true,
                        ),
                        'version'     => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'action'      => array(
                            'type'    => 'string',
                            'length'  => 20,
                            'notnull' => true,
                        ),
                        'comment'     => array(
                            'type' => 'string',
                        ),
                        'create_time' => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'create_uid'  => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                    )
                ),
                DB_PREFIX . 'element_tree'                  => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id'),
                    ),
                    'definition' => array(
                        'id'              => array(
                            'type'          => 'integer',
                            'unsigned'      => true,
                            'notnull'       => true,
                            'autoincrement' => true,
                        ),
                        'parent_id'       => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'siteroot_id'     => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'eid'             => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'type'            => array(
                            'type'    => 'string',
                            'length'  => 20,
                            'notnull' => true,
                        ),
                        'sort'            => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'default'  => 0,
                            'unsigned' => true,
                        ),
                        'sort_mode'       => array(
                            'type'    => 'string',
                            'length'  => 20,
                            'default' => 'title'
                        ),
                        'sort_dir'        => array(
                            'type'    => 'string',
                            'length'  => 4,
                            'default' => 'asc'
                        ),
                        'instance_master' => array(
                            'type' => 'boolean',
                        ),
                        'modify_uid'      => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'modify_time'     => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                    )
                ),
                DB_PREFIX . 'element_tree_context'          => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('tid', 'context'),
                    ),
                    'definition' => array(
                        'tid'     => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'context' => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'length'  => 255,
                        ),
                    ),
                ),
                DB_PREFIX . 'element_tree_hash'             => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('tid', 'version', 'language'),
                    ),
                    'definition' => array(
                        'tid'      => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'version'  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language' => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'length'  => 2,
                        ),
                        'hash'     => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'fixed'   => true,
                            'length'  => 32,
                        ),
                        'debug'    => array(
                            'type' => 'clob',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_tree_history'          => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id'),
                    ),
                    'definition' => array(
                        'id'          => array(
                            'type'          => 'integer',
                            'notnull'       => true,
                            'unsigned'      => true,
                            'autoincrement' => true,
                        ),
                        'tid'         => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'eid'         => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language'    => array(
                            'type'   => 'string',
                            'length' => 2,
                            'fixed'  => true,
                        ),
                        'version'     => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'action'      => array(
                            'type'    => 'string',
                            'length'  => 20,
                            'notnull' => true,
                        ),
                        'comment'     => array(
                            'type' => 'string',
                        ),
                        'create_time' => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'create_uid'  => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                    )
                ),
                DB_PREFIX . 'element_tree_online'           => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('tree_id', 'language')
                    ),
                    'definition' => array(
                        'tree_id'      => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'eid'          => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'language'     => array(
                            'type'   => 'string',
                            'length' => 2,
                            'fixed'  => true
                        ),
                        'version'      => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'publish_uid'  => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'publish_time' => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                    )
                ),
                DB_PREFIX . 'element_tree_page'             => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('tree_id', 'version')
                    ),
                    'definition' => array(
                        'tree_id'        => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'eid'            => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'version'        => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'navigation'     => array(
                            'type'    => 'boolean',
                            'notnull' => true,
                        ),
                        'restricted'     => array(
                            'type'    => 'boolean',
                            'notnull' => true,
                        ),
                        'disable_cache'  => array(
                            'type'    => 'boolean',
                            'notnull' => true,
                        ),
                        'cache_lifetime' => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 0,
                        ),
                        'code'           => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 200,
                        ),
                        'https'          => array(
                            'type'    => 'boolean',
                            'notnull' => true,
                            'default' => false
                        ),
                    )
                ),
                DB_PREFIX . 'element_version'               => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array(
                            'eid',
                            'version'
                        ),
                    ),
                    'definition' => array(
                        'eid'                  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'version'              => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'element_type_id'      => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'element_type_version' => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'format'               => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            'default'  => 1
                        ),
                        'title'                => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'comment'              => array(
                            'type' => 'clob',
                        ),
                        'minor'                => array(
                            'type'    => 'boolean',
                            'default' => 0,
                            'notnull' => true,
                        ),
                        'trigger_language'     => array(
                            'type'   => 'char',
                            'length' => 2,
                            'fixed'  => true,
                        ),
                        'create_uid'           => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'create_time'          => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'publish_uid'          => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'publish_time'         => array(
                            'type' => 'timestamp',
                        ),
                    )
                ),
                DB_PREFIX . 'element_version_metaset_items' => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id')
                    ),
                    'definition' => array(
                        'id'       => array(
                            'type'          => 'integer',
                            'unsigned'      => true,
                            'autoincrement' => true,
                            'notnull'       => true,
                        ),
                        'set_id'   => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'eid'      => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'version'  => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'language' => array(
                            'type'   => 'string',
                            'length' => 2,
                            'fixed'  => true
                        ),
                        'key'      => array(
                            'type'    => 'string',
                            'length'  => 100,
                            'notnull' => true,
                        ),
                        'value'    => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                    ),
                ),
                DB_PREFIX . 'element_version_titles'        => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('eid', 'version', 'language')
                    ),
                    'definition' => array(
                        'eid'        => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'version'    => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language'   => array(
                            'type'    => 'string',
                            'length'  => 2,
                            'notnull' => true,
                            'fixed'   => true,
                        ),
                        'backend'    => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'page'       => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'navigation' => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'date'       => array(
                            'type'    => 'string',
                            'length'  => 19,
                            'notnull' => true,
                        ),
                        'custom_1'   => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'custom_2'   => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'custom_3'   => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'custom_4'   => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                        'custom_5'   => array(
                            'type'   => 'string',
                            'length' => 255,
                        ),
                    ),
                ),
                DB_PREFIX . 'element_notifications'         => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id'),
                    ),
                    'definition' => array(
                        'id'          => array(
                            'type'          => 'integer',
                            'notnull'       => true,
                            'unsigned'      => true,
                            'autoincrement' => true
                        ),
                        'tid'         => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language'    => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'length'  => 2,
                        ),
                        'create_time' => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'update_time' => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                    ),
                ),
            ),
        ),
        // createIndex
        array(
            'action' => 'createIndex',
            'data'   => array(
                DB_PREFIX . 'element'                       => array(
                    'element_idx_0' => array(
                        'type'   => 'unique',
                        'fields' => array(
                            'unique_id',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_data'                  => array(
                    'element_data_idx_0' => array(
                        'fields' => array(
                            'eid',
                            'version',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_history'               => array(
                    'element_history_idx_0' => array(
                        'fields' => array(
                            'eid',
                            'language',
                            'version',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_version'               => array(
                    'element_version_idx_0' => array(
                        'fields' => array(
                            'element_type_id',
                            'element_type_version',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_version_metaset_items' => array(
                    'element_version_metaset_items_eid'   => array(
                        'fields' => array(
                            'eid',
                            'version',
                            'language',
                        ),
                    ),
                    'element_version_metaset_items_setid' => array(
                        'fields' => array(
                            'set_id',
                            'eid',
                            'version',
                        ),
                    ),
                    'element_version_metaset_items_key'   => array(
                        'fields' => array(
                            'key',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_tree'                  => array(
                    'eid_siteroot_id' => array(
                        'fields' => array(
                            'eid'         => array(),
                            'siteroot_id' => array(),
                        ),
                        'unique' => true,
                    )
                ),
            ),
        ),
        // createForeignKey
        array(
            'action' => 'createForeignKey',
            'data'   => array(
                DB_PREFIX . 'element_data'                  => array(
                    array(
                        'name'         => 'element_version_to_element_data',
                        'local'        => array('eid', 'version'),
                        'foreign'      => array('eid', 'version'),
                        'foreignTable' => DB_PREFIX . 'element_version',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_data_language'         => array(
                    array(
                        'name'         => 'element_data_to_element_data_language',
                        'local'        => array('eid', 'version', 'data_id'),
                        'foreign'      => array('eid', 'version', 'data_id'),
                        'foreignTable' => DB_PREFIX . 'element_data',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_history'               => array(
                    array(
                        'name'         => 'element_version_to_element_history',
                        'local'        => array('eid', 'version'),
                        'foreign'      => array('eid', 'version'),
                        'foreignTable' => DB_PREFIX . 'element_version',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree'                  => array(
                    array(
                        'name'         => 'element_tree_to_element',
                        'local'        => 'eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'element_tree_to_siteroot',
                        'local'        => 'siteroot_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'siteroot',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'ett_to_ettp',
                        'local'        => array('parent_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_context'          => array(
                    array(
                        'name'         => 'element_tree_to_element_tree_context',
                        'local'        => 'tid',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_hash'             => array(
                    array(
                        'name'         => 'element_tree_to_element_tree_hash',
                        'local'        => array('tid'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_online'           => array(
                    array(
                        'name'         => 'element_tree_to_element_tree_online',
                        'local'        => array('tree_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'eto_to_e',
                        'local'        => 'eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_page'             => array(
                    array(
                        'name'         => 'element_version_to_element_tree_page',
                        'local'        => array('eid', 'version'),
                        'foreign'      => array('eid', 'version'),
                        'foreignTable' => DB_PREFIX . 'element_version',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'etp_to_e',
                        'local'        => 'eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'element_tree_to_element_tree_page',
                        'local'        => array('tree_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_version'               => array(
                    array(
                        'name'         => 'elementtype_version_to_element_version',
                        'local'        => array('element_type_id', 'element_type_version'),
                        'foreign'      => array('element_type_id', 'version'),
                        'foreignTable' => DB_PREFIX . 'elementtype_version',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'element_to_element_version',
                        'local'        => 'eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_version_metaset_items' => array(
                    array(
                        'name'         => 'element_version_meta_to_element_version',
                        'local'        => array('eid', 'version'),
                        'foreign'      => array('eid', 'version'),
                        'foreignTable' => DB_PREFIX . 'element_version',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_version_titles'        => array(
                    array(
                        'name'         => 'element_version_titles_to_element_version',
                        'local'        => array('eid', 'version'),
                        'foreign'      => array('eid', 'version'),
                        'foreignTable' => DB_PREFIX . 'element_version',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_notifications'         => array(
                    array(
                        'name'         => 'element_notifications_to_element_tree',
                        'local'        => 'tid',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
            ),
        ),
    ),
);
