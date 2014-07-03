<?php

$setup = array(
    'database' => array(
        array(
            'action' => 'createTable',
            'data'   => array(
                DB_PREFIX . 'element_tree_teasers'          => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id'),
                    ),
                    'definition' => array(
                        'id'             => array(
                            'type'          => 'integer',
                            'notnull'       => true,
                            'autoincrement' => true,
                            'unsigned'      => true,
                        ),
                        'tree_id'        => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'eid'            => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'layoutarea_id'  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'teaser_eid'     => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                        'type'           => array(
                            'type'    => 'string',
                            'length'  => 20,
                            'notnull' => true,
                            'default' => 'teaser'
                        ),
                        'template_id'    => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'configuration'  => array(
                            'type' => 'clob',
                        ),
                        'inherit'        => array(
                            'type' => 'boolean',
                        ),
                        'stop_inherit'   => array(
                            'type' => 'boolean',
                        ),
                        'sort'           => array(
                            'type'    => 'integer',
                            'notnull' => true,
                            'default' => 0,
                        ),
                        'modify_uid'     => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'modify_time'    => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                        'publish_uid'    => array(
                            'type'   => 'string',
                            'length' => 36,
                            'fixed'  => true,
                        ),
                        'publish_time'   => array(
                            'type' => 'timestamp',
                        ),
                        'display'        => array(
                            'type'    => 'boolean',
                            'default' => true
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
                    )
                ),
                DB_PREFIX . 'element_tree_teasers_context'  => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('teaser_id', 'context'),
                    ),
                    'definition' => array(
                        'teaser_id' => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'context'   => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'length'  => 255,
                        ),
                    ),
                ),
                DB_PREFIX . 'element_tree_teasers_hash'     => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('teaser_id', 'version', 'language'),
                    ),
                    'definition' => array(
                        'teaser_id' => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'version'   => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                        'language'  => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'length'  => 2,
                        ),
                        'hash'      => array(
                            'type'    => 'string',
                            'notnull' => true,
                            'fixed'   => true,
                            'length'  => 32,
                        ),
                        'debug'     => array(
                            'type' => 'clob',
                        ),
                    ),
                ),
                DB_PREFIX . 'element_tree_teasers_online'   => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('teaser_id', 'language')
                    ),
                    'definition' => array(
                        'teaser_id'    => array(
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
                DB_PREFIX . 'element_tree_teasers_rotation' => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('teaser_id'),
                    ),
                    'definition' => array(
                        'teaser_id' => array(
                            'type'          => 'integer',
                            'notnull'       => true,
                            'autoincrement' => true,
                            'unsigned'      => true,
                        ),
                        'position'  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                        ),
                    )
                ),
                DB_PREFIX . 'catchteaser_helper'            => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('eid', 'version', 'tid', 'preview', 'language'),
                    ),
                    'definition' => array(
                        'eid'            => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            'primary'  => true
                        ),
                        'version'        => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            'primary'  => true
                        ),
                        'tid'            => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            'primary'  => true
                        ),
                        'preview'        => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true,
                            'primary'  => true
                        ),
                        'elementtype_id' => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true
                        ),
                        'in_navigation'  => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true
                        ),
                        'restricted'     => array(
                            'type'     => 'integer',
                            'notnull'  => true,
                            'unsigned' => true
                        ),
                        'publish_time'   => array(
                            'type' => 'timestamp',
                        ),
                        'custom_date'    => array(
                            'type' => 'date',
                        ),
                        'language'       => array(
                            'type'    => 'string',
                            'length'  => 2,
                            'notnull' => true,
                        ),
                        'online_version' => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                        ),
                    )
                ),
                DB_PREFIX . 'catchteaser_metaset_items'     => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb'
                    ),
                    'definition' => array(
                        'id'       => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'set_id'   => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'tid'      => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
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
            )
        ),
        // createIndex
        array(
            'action' => 'createIndex',
            'data'   => array(
                DB_PREFIX . 'element_tree_teasers'      => array(
                    'element_teasers_teaser_eid'    => array(
                        'fields' => array(
                            'teaser_eid',
                        ),
                    ),
                    'element_teasers_tree_id'       => array(
                        'fields' => array(
                            'tree_id',
                        ),
                    ),
                    'element_teasers_eid'           => array(
                        'fields' => array(
                            'eid',
                        ),
                    ),
                    'element_teasers_layoutarea_id' => array(
                        'fields' => array(
                            'layoutarea_id',
                        ),
                    ),
                ),
                DB_PREFIX . 'catchteaser_helper'        => array(
                    'catchteaser_helper_tid'            => array(
                        'fields' => array(
                            'tid',
                            'language',
                            'preview',
                            'restricted'
                        ),
                    ),
                    'catchteaser_helper_eid'            => array(
                        'fields' => array(
                            'eid',
                            'language',
                            'version',
                            'preview',
                            'restricted'
                        ),
                    ),
                    'catchteaser_helper_elementtype_id' => array(
                        'fields' => array(
                            'elementtype_id',
                            'language',
                            'preview',
                            'restricted'
                        ),
                    ),
                    'catchteaser_helper_publish_time'   => array(
                        'fields' => array(
                            'publish_time'
                        ),
                    ),
                    'catchteaser_helper_custom_date'    => array(
                        'fields' => array(
                            'custom_date'
                        ),
                    ),
                ),
                DB_PREFIX . 'catchteaser_metaset_items' => array(
                    'catchteaser_metaset_items_eid'     => array(
                        'fields' => array(
                            'eid'
                        ),
                    ),
                    'catchteaser_metaset_items_version' => array(
                        'fields' => array(
                            'version'
                        ),
                    ),
                    'catchteaser_metaset_items_value'   => array(
                        'fields' => array(
                            'value'
                        ),
                    ),
                ),
            ),
        ),
        // createForeignKey
        array(
            'action' => 'createForeignKey',
            'data'   => array(
                DB_PREFIX . 'element_tree_teasers'          => array(
                    array(
                        'name'         => 'ett_to_element',
                        'local'        => 'eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'ett_to_element_2',
                        'local'        => 'teaser_eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'ett_to_element_tree',
                        'local'        => 'tree_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'element_tree',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'ett_to_elementtype',
                        'local'        => 'layoutarea_id',
                        'foreign'      => 'element_type_id',
                        'foreignTable' => DB_PREFIX . 'elementtype',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_teasers_context'  => array(
                    array(
                        'name'         => 'ett_to_ett_context',
                        'local'        => 'teaser_id',
                        'foreign'      => 'id',
                        'foreignTable' => DB_PREFIX . 'element_tree_teasers',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_teasers_hash'     => array(
                    array(
                        'name'         => 'ett_to_ett_hash',
                        'local'        => array('teaser_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree_teasers',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_teasers_online'   => array(
                    array(
                        'name'         => 'ett_etto',
                        'local'        => array('teaser_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree_teasers',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                    array(
                        'name'         => 'etto_to_e',
                        'local'        => 'eid',
                        'foreign'      => 'eid',
                        'foreignTable' => DB_PREFIX . 'element',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
                DB_PREFIX . 'element_tree_teasers_rotation' => array(
                    array(
                        'name'         => 'ett_ettr',
                        'local'        => array('teaser_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'element_tree_teasers',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE',
                    ),
                ),
            ),
        ),
    ),
);
