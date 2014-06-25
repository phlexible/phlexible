<?php

namespace Phlexible\Bundle\TreeBundle\Schema;

class Install
{
    public function install(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $tree = $schema->createTable('tree');
        $tree->addColumn('id', 'integer', array('unsigned' => true));
        $tree->addColumn('parent_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $tree->addColumn('siteroot_id', 'string', array('length' => 36, 'fixed' => true));
        $tree->addColumn('type', 'string', array('length' => 50));
        $tree->addColumn('type_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $tree->addColumn('attributes', 'text', array('notnull' => false));
        $tree->addColumn('sort', 'integer', array('unsigned' => true));
        $tree->addColumn('sort_mode', 'string', array('length' => 20, 'default' => 'title'));
        $tree->addColumn('sort_dir', 'string', array('length' => 4, 'default' => 'asc'));
        $tree->addColumn('create_uid', 'string', array('length' => 36, 'fixed' => true));
        $tree->addColumn('create_time', 'datetime');
        $tree->setPrimaryKey(array('id'));

        $treeHistory = $schema->createTable('tree_history');
        $treeHistory->addColumn('id', 'integer', array('unsigned' => true));
        $treeHistory->addColumn('tree_id', 'integer', array('unsigned' => true));
        $treeHistory->addColumn('action', 'string', array('length' => 50));
        $treeHistory->addColumn('comment', 'text', array('notnull' => false));
        $treeHistory->addColumn('create_uid', 'string', array('length' => 36, 'fixed' => true));
        $treeHistory->addColumn('create_time', 'datetime');
        $treeHistory->setPrimaryKey(array('id'));
        $treeHistory->addForeignKeyConstraint('tree', array('tree_id'), array('id'));

        $treeOnline = $schema->createTable('tree_online');
        $treeOnline->addColumn('tree_id', 'integer', array('unsigned' => true));
        $treeOnline->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $treeOnline->addColumn('version', 'integer', array('unsigned' => true));
        $treeOnline->addColumn('publish_user_id', 'string', array('length' => 36, 'fixed' => true));
        $treeOnline->addColumn('published_at', 'datetime');
        $treeOnline->setPrimaryKey(array('tree_id', 'language'));
        $treeOnline->addForeignKeyConstraint('tree', array('tree_id'), array('id'));
    }
}