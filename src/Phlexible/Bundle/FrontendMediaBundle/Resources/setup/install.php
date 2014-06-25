<?php

$setup = array(
    'database' => array(
        array(
            'action' => 'createTable',
            'data'   => array(
                DB_PREFIX . 'mediamanager_files_downloads_frontend' => array(
                    'options' => array(
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci',
                        'type'    => 'innodb',
                        'primary' => array('file_id'),
                    ),
                    'definition' => array (
                        'file_id' => array (
                            'type'    => 'string',
                            'length'  => 36,
                            'fixed'   => true,
                            'notnull' => true,
                        ),
                        'cnt' => array (
                            'type'     => 'integer',
                            'unsigned' => true,
                            'notnull'  => true,
                            'default'  => 0
                        ),
                        'last_download' => array (
                            'type'    => 'timestamp'
                        ),
                    ),
                ),
            ),
        ),

        array(
            'action' => 'createForeignKey',
            'data'   => array(
                DB_PREFIX . 'mediamanager_files_downloads_frontend' => array(
                    array(
                        'name'         => 'mediamanager_files_to_mediamanager_files_downloads_frontend',
                        'local'        => array('file_id'),
                        'foreign'      => array('id'),
                        'foreignTable' => DB_PREFIX . 'mediamanager_files',
                        'onDelete'     => 'CASCADE',
                        'onUpdate'     => 'CASCADE'
                    ),
                ),
            ),
        ),
    ),
);
