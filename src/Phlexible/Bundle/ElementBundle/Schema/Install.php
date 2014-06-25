<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Schema;

use Doctrine\DBAL\Schema\Schema;

class Install
{
    /**
     * @param Schema $schema
     */
    public function install(Schema $schema)
    {
        $element = $schema->createTable('element');
        $element->addColumn('eid', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $element->addColumn('unique_id', 'string', array('length' => 255, 'notnull' => false));
        $element->addColumn('elementtype_id', 'integer', array('unsigned' => true));
        $element->addColumn('masterlanguage', 'string', array('length' => 2, 'fixed' => true));
        $element->addColumn('latest_version', 'integer', array('unsigned' => true));
        $element->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $element->addColumn('create_time', 'datetime');
        $element->addColumn('modify_user_id', 'string', array('length' => 36, 'fixed' => true));
        $element->addColumn('modify_time', 'datetime');
        $element->setPrimaryKey(array('eid'));
        $element->addIndex(array('unique_id'));

        $elementVersion = $schema->createTable('element_version');
        $elementVersion->addColumn('eid', 'integer', array('unsigned' => true));
        $elementVersion->addColumn('version', 'integer', array('unsigned' => true));
        $elementVersion->addColumn('elementtype_version', 'integer', array('unsigned' => true));
        $elementVersion->addColumn('minor', 'boolean', array('default' => 0));
        $elementVersion->addColumn('format', 'integer', array('unsigned' => true, 'default' => 1));
        $elementVersion->addColumn('trigger_language', 'string', array('length' => 2, 'fixed' => true, 'notnull' => false));
        $elementVersion->addColumn('mapped_fields', 'text', array('notnull' => false));
        $elementVersion->addColumn('comment', 'text', array('notnull' => false));
        $elementVersion->addColumn('create_uid', 'string', array('length' => 36, 'fixed' => true));
        $elementVersion->addColumn('create_time', 'datetime');
        $elementVersion->setPrimaryKey(array('eid', 'version'));
        $elementVersion->addForeignKeyConstraint('element', array('eid'), array('eid'));

        $elementVersionTitle = $schema->createTable('element_version_mappedfield');
        $elementVersionTitle->addColumn('eid', 'integer', array('unsigned' => true));
        $elementVersionTitle->addColumn('version', 'integer', array('unsigned' => true));
        $elementVersionTitle->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $elementVersionTitle->addColumn('backend', 'string', array('length' => 255));
        $elementVersionTitle->addColumn('page', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('navigation', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('date', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('forward', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('custom1', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('custom2', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('custom3', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('custom4', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addColumn('custom5', 'string', array('length' => 255, 'notnull' => false));
        $elementVersionTitle->addForeignKeyConstraint('element_version', array('eid', 'version'), array('eid', 'version'));

        $elementHistory = $schema->createTable('element_history');
        $elementHistory->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $elementHistory->addColumn('eid', 'integer', array('unsigned' => true));
        $elementHistory->addColumn('language', 'string', array('length' => 2, 'fixed' => true, 'notnull' => false));
        $elementHistory->addColumn('version', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementHistory->addColumn('action', 'string', array('length' => 255));
        $elementHistory->addColumn('comment', 'text', array('notnull' => false));
        $elementHistory->addColumn('create_uid', 'string', array('length' => 36, 'fixed' => true));
        $elementHistory->addColumn('create_time', 'datetime');
        $elementHistory->setPrimaryKey(array('id'));
        $elementHistory->addForeignKeyConstraint('element', array('eid'), array('eid'));
        $elementHistory->addIndex(array('eid', 'language', 'version'));

        $elementData = $schema->createTable('element_data');
        $elementData->addColumn('data_id', 'integer', array('unsigned' => true));
        $elementData->addColumn('eid', 'integer', array('unsigned' => true));
        $elementData->addColumn('version', 'integer', array('unsigned' => true));
        $elementData->addColumn('cnt', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementData->addColumn('ds_id', 'string', array('length' => 36, 'fixed' => true));
        $elementData->addColumn('title', 'string', array('length' => 255, 'notnull' => false));
        $elementData->addColumn('repeatable_node', 'boolean', array('default' => 0));
        $elementData->addColumn('repeatable_id', 'string', array('length' => 255, 'notnull' => false));
        $elementData->addColumn('repeatable_ds_id', 'string', array('length' => 36, 'fixed' => true, 'notnull' => false));
        $elementData->addColumn('sort', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementData->setPrimaryKey(array('data_id', 'eid', 'version'));
        $elementData->addForeignKeyConstraint('element_version', array('eid', 'version'), array('eid', 'version'));

        $elementDataLanguage = $schema->createTable('element_data_language');
        $elementDataLanguage->addColumn('data_id', 'integer', array('unsigned' => true));
        $elementDataLanguage->addColumn('eid', 'integer', array('unsigned' => true));
        $elementDataLanguage->addColumn('version', 'integer', array('unsigned' => true));
        $elementDataLanguage->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $elementDataLanguage->addColumn('content', 'text', array('notnull' => false));
        $elementDataLanguage->addColumn('options', 'string', array('length' => 255, 'notnull' => false));
        $elementDataLanguage->setPrimaryKey(array('data_id', 'eid', 'version', 'language'));
        $elementDataLanguage->addForeignKeyConstraint('element_data', array('eid', 'version', 'data_id'), array('eid', 'version', 'data_id'));

        $elementStructure = $schema->createTable('element_structure');
        $elementStructure->addColumn('data_id', 'integer', array('unsigned' => true));
        $elementStructure->addColumn('eid', 'integer', array('unsigned' => true));
        $elementStructure->addColumn('version', 'integer', array('unsigned' => true));
        $elementStructure->addColumn('ds_id', 'string', array('length' => 36, 'fixed' => true));
        $elementStructure->addColumn('type', 'string', array('length' => 255, 'notnull' => false));
        $elementStructure->addColumn('name', 'string', array('length' => 255, 'notnull' => false));
        $elementStructure->addColumn('cnt', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementStructure->addColumn('repeatable_node', 'boolean', array('default' => 0));
        $elementStructure->addColumn('repeatable_id', 'string', array('length' => 255, 'notnull' => false));
        $elementStructure->addColumn('repeatable_ds_id', 'string', array('length' => 36, 'fixed' => true, 'notnull' => false));
        $elementStructure->addColumn('sort', 'integer', array('unsigned' => true, 'notnull' => false));
        $elementStructure->setPrimaryKey(array('data_id', 'eid', 'version'));
        $elementStructure->addForeignKeyConstraint('element_version', array('eid', 'version'), array('eid', 'version'));

        $elementStructureData = $schema->createTable('element_structure_data');
        $elementStructureData->addColumn('data_id', 'integer', array('unsigned' => true));
        $elementStructureData->addColumn('eid', 'integer', array('unsigned' => true));
        $elementStructureData->addColumn('version', 'integer', array('unsigned' => true));
        $elementStructureData->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $elementStructureData->addColumn('ds_id', 'string', array('length' => 36, 'fixed' => true));
        $elementStructureData->addColumn('type', 'string', array('length' => 255, 'notnull' => false));
        $elementStructureData->addColumn('name', 'string', array('length' => 255, 'notnull' => false));
        $elementStructureData->addColumn('repeatable_id', 'string', array('length' => 255, 'notnull' => false));
        $elementStructureData->addColumn('repeatable_ds_id', 'string', array('length' => 36, 'fixed' => true, 'notnull' => false));
        $elementStructureData->addColumn('content', 'text', array('notnull' => false));
        $elementStructureData->addColumn('options', 'string', array('length' => 255, 'notnull' => false));
        $elementStructureData->setPrimaryKey(array('data_id', 'eid', 'version', 'language'));
        $elementStructureData->addForeignKeyConstraint('element_version', array('eid', 'version'), array('eid', 'version'));

        $elementOnline = $schema->createTable('element_link');
        $elementOnline->addColumn('eid', 'integer', array('unsigned' => true));
        $elementOnline->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $elementOnline->addColumn('version', 'integer', array('unsigned' => true));
        $elementOnline->addColumn('type', 'string', array('length' => 50));
        $elementOnline->addColumn('target', 'string', array('length' => 255));
        $elementOnline->setPrimaryKey(array('eid', 'version', 'language'));
    }
}