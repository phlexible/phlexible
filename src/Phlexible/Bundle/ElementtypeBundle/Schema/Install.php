<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Schema;

use Doctrine\DBAL\Schema\Schema;

class Install
{
    /**
     * @param Schema $schema
     */
    public function install(Schema $schema)
    {
        $elementtype = $schema->createTable('elementtype');
        $elementtype->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $elementtype->addColumn('unique_id', 'string', array('length' => 255));
        $elementtype->addColumn('type', 'string', array('length' => 20));
        $elementtype->addColumn('title', 'string', array('length' => 100));
        $elementtype->addColumn('icon', 'string', array('length' => 255, 'notnull' => false));
        $elementtype->addColumn('hide_children', 'boolean', array('length' => 100));
        $elementtype->addColumn('default_tab', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementtype->addColumn('deleted', 'boolean', array('default' => 0));
        $elementtype->addColumn('latest_version', 'integer', array('unsigned' => true));
        $elementtype->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $elementtype->addColumn('created_at', 'datetime');
        $elementtype->setPrimaryKey(array('id'));
        $elementtype->addUniqueIndex(array('unique_id'));

        $elementtypeVersion = $schema->createTable('elementtype_version');
        $elementtypeVersion->addColumn('elementtype_id', 'integer', array('unsigned' => true));
        $elementtypeVersion->addColumn('version', 'integer', array('unsigned' => true));
        $elementtypeVersion->addColumn('default_content_tab', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementtypeVersion->addColumn('filter', 'boolean', array('default' => 0));
        $elementtypeVersion->addColumn('mappings', 'text', array('notnull' => false));
        $elementtypeVersion->addColumn('comment', 'text', array('notnull' => false));
        $elementtypeVersion->addColumn('metaset_id', 'string', array('length' => 36, 'fixed' => true, 'notnull' => false));
        $elementtypeVersion->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $elementtypeVersion->addColumn('created_at', 'datetime');
        $elementtypeVersion->setPrimaryKey(array('elementtype_id', 'version'));
        $elementtypeVersion->addForeignKeyConstraint('elementtype', array('elementtype_id'), array('id'));

        $elementtypeStructure = $schema->createTable('elementtype_structure');
        $elementtypeStructure->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $elementtypeStructure->addColumn('elementtype_id', 'integer', array('unsigned' => true));
        $elementtypeStructure->addColumn('elementtype_version', 'integer', array('unsigned' => true));
        $elementtypeStructure->addColumn('ds_id', 'string', array('length' => 36, 'fixed' => true));
        $elementtypeStructure->addColumn('parent_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementtypeStructure->addColumn('parent_ds_id', 'string', array('length' => 36, 'fixed' => true, 'notnull' => false));
        $elementtypeStructure->addColumn('sort', 'integer', array('unsigned' => true));
        $elementtypeStructure->addColumn('reference_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementtypeStructure->addColumn('reference_version', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementtypeStructure->addColumn('type', 'string', array('length' => 20));
        $elementtypeStructure->addColumn('name', 'string', array('length' => 100));
        $elementtypeStructure->addColumn('configuration', 'text', array('notnull' => false));
        $elementtypeStructure->addColumn('validation', 'text', array('notnull' => false));
        $elementtypeStructure->addColumn('labels', 'text', array('notnull' => false));
        $elementtypeStructure->addColumn('options', 'text', array('notnull' => false));
        $elementtypeStructure->addColumn('content_channels', 'text', array('notnull' => false));
        $elementtypeStructure->addColumn('comment', 'text', array('notnull' => false));
        $elementtypeStructure->setPrimaryKey(array('id'));
        $elementtypeStructure->addForeignKeyConstraint('elementtype_structure', array('parent_id'), array('id'));
        $elementtypeStructure->addForeignKeyConstraint('elementtype_version', array('elementtype_id', 'elementtype_version'), array('elementtype_id', 'version'));
        $elementtypeStructure->addIndex(array('type'));

        $elementtypeApply = $schema->createTable('elementtype_apply');
        $elementtypeApply->addColumn('elementtype_id', 'integer', array('unsigned' => true));
        $elementtypeApply->addColumn('apply_under_id', 'integer', array('unsigned' => true));
        $elementtypeApply->setPrimaryKey(array('elementtype_id', 'apply_under_id'));
        $elementtypeApply->addForeignKeyConstraint('elementtype', array('apply_under_id'), array('id'));
        $elementtypeApply->addForeignKeyConstraint('elementtype', array('elementtype_id'), array('id'));
    }
}