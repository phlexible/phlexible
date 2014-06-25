<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Schema;

use Doctrine\DBAL\Schema\Schema;

class Install
{
    /**
     * @param Schema $schema
     */
    public function install(Schema $schema)
    {
        $siteroot = $schema->createTable('siteroot');
        $siteroot->addColumn('id', 'string', array('length' => 36, 'fixed' => true));
        $siteroot->addColumn('default', 'boolean', array('default' => false));
        $siteroot->addColumn('head_title', 'string', array('length' => 255, 'notnull' => false));
        $siteroot->addColumn('start_head_title', 'string', array('length' => 255, 'notnull' => false));
        //$siteroot->addColumn('contentchannel_id', 'integer', array('unsigned' => true));
        $siteroot->addColumn('create_time', 'datetime');
        $siteroot->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $siteroot->addColumn('modify_time', 'datetime');
        $siteroot->addColumn('modify_user_id', 'string', array('length' => 36, 'fixed' => true));
        $siteroot->setPrimaryKey(array('id'));

        $siterootContentchannel = $schema->createTable('siteroot_contentchannel');
        $siterootContentchannel->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootContentchannel->addColumn('contentchannel_id', 'integer', array('unsigned' => true));
        $siterootContentchannel->addColumn('default', 'boolean', array('default' => false));
        $siterootContentchannel->setPrimaryKey(array('siteroot_id', 'contentchannel_id'));
        //$siterootContentchannel->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));

        $siterootNavigation = $schema->createTable('siteroot_navigation');
        $siterootNavigation->addColumn('id', 'string', array('length' => 36, 'fixed' => true));
        $siterootNavigation->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootNavigation->addColumn('title', 'string', array('length' => 255));
        $siterootNavigation->addColumn('handler', 'string', array('length' => 255));
        $siterootNavigation->addColumn('start_tree_id', 'integer', array('unsigned' => true));
        $siterootNavigation->addColumn('max_depth', 'integer', array('unsigned' => true));
        $siterootNavigation->addColumn('flags', 'integer', array('unsigned' => true));
        $siterootNavigation->addColumn('additional', 'text', array('notnull' => false));
        $siterootNavigation->setPrimaryKey(array('id'));
        $siterootNavigation->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));

        $siterootProperty = $schema->createTable('siteroot_property');
        $siterootProperty->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootProperty->addColumn('key', 'string', array('length' => 255));
        $siterootProperty->addColumn('value', 'string', array('length' => 255, 'notnull' => false));
        $siterootProperty->setPrimaryKey(array('siteroot_id', 'key'));
        $siterootProperty->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));

        $siterootShortUrl = $schema->createTable('siteroot_shorturl');
        $siterootShortUrl->addColumn('id', 'string', array('length' => 36, 'fixed' => true));
        $siterootShortUrl->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootShortUrl->addColumn('hostname', 'string', array('length' => 255));
        $siterootShortUrl->addColumn('path', 'string', array('length' => 255, 'notnull' => false));
        $siterootShortUrl->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $siterootShortUrl->addColumn('type', 'string', array('length' => 1, 'fixed' => true));
        $siterootShortUrl->addColumn('target', 'string', array('length' => 255));
        $siterootShortUrl->setPrimaryKey(array('id'));
        $siterootShortUrl->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));

        $siterootSpecialTid = $schema->createTable('siteroot_specialtid');
        $siterootSpecialTid->addColumn('id', 'string', array('length' => 36, 'fixed' => true));
        $siterootSpecialTid->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootSpecialTid->addColumn('key', 'string', array('length' => 255));
        $siterootSpecialTid->addColumn('language', 'string', array('length' => 2, 'fixed' => true, 'notnull' => false));
        $siterootSpecialTid->addColumn('tree_id', 'integer', array('unsigned' => true));
        $siterootSpecialTid->setPrimaryKey(array('id'));
        $siterootSpecialTid->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));

        $siterootTitle = $schema->createTable('siteroot_title');
        $siterootTitle->addColumn('id', 'string', array('length' => 36, 'fixed' => true));
        $siterootTitle->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootTitle->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $siterootTitle->addColumn('title', 'string', array('length' => 255));
        $siterootTitle->setPrimaryKey(array('id'));
        $siterootTitle->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));
        $siterootTitle->addUniqueIndex(array('siteroot_id', 'title', 'language'));

        $siterootUrl = $schema->createTable('siteroot_url');
        $siterootUrl->addColumn('id', 'string', array('length' => 36, 'fixed' => true));
        $siterootUrl->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $siterootUrl->addColumn('global_default', 'boolean', array('default' => false));
        $siterootUrl->addColumn('default', 'boolean', array('default' => false));
        $siterootUrl->addColumn('hostname', 'string', array('length' => 255));
        $siterootUrl->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $siterootUrl->addColumn('type', 'string', array('length' => 1, 'fixed' => true));
        $siterootUrl->addColumn('target', 'string', array('length' => 255));
        $siterootUrl->setPrimaryKey(array('id'));
        $siterootUrl->addForeignKeyConstraint('siteroot', array('siteroot_id'), array('id'));
        $siterootUrl->addUniqueIndex(array('siteroot_id', 'hostname', 'language'));
        $siterootUrl->addIndex(array('hostname'));
    }
}