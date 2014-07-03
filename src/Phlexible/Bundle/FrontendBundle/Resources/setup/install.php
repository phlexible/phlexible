<?php

$setup = array(
    'database' => array(
        array(
            'action' => 'createTable',
            'data'   => array(
                DB_PREFIX . 'request_urls' => array(
                    'options'    => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('id', 'siteroot_id'),
                    ),
                    'definition' => array(
                        'id'          => array(
                            'type'    => 'string',
                            'length'  => 255,
                            'notnull' => true,
                        ),
                        'siteroot_id' => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'tid'         => array(
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                        ),
                        'language'    => array(
                            'type'    => 'string',
                            'length'  => 2,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'active'      => array(
                            'type'    => 'boolean',
                            'notnull' => true,
                            'default' => 0,
                        ),
                        'create_uid'  => array(
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'create_time' => array(
                            'type'    => 'timestamp',
                            'notnull' => true,
                        ),
                    )
                ),
            ),
        ),
        // createIndex
        array(
            'action' => 'createIndex',
            'data'   => array(
                DB_PREFIX . 'request_urls' => array(
                    'request_urls_0' => array(
                        'fields' => array(
                            'siteroot_id',
                            'tid',
                            'language'
                        ),
                    ),
                ),
            ),
        ),
    ),
);
