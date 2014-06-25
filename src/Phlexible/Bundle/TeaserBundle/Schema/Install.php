<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Schema;

use Doctrine\DBAL\Schema\Schema;

class Install
{
    /**
     * @param Schema $schema
     */
    public function install(Schema $schema)
    {
        $teaser = $schema->createTable('teaser');
        $teaser->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $teaser->addColumn('tree_id', 'integer', array('unsigned' => true));
        $teaser->addColumn('eid', 'integer', array('unsigned' => true));
        $teaser->addColumn('layoutarea_id', 'integer', array('unsigned' => true));
        $teaser->addColumn('type', 'string', array('length' => 20));
        $teaser->addColumn('type_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $teaser->addColumn('template_id', 'string', array('length' => 36, 'fixed' => true, 'notnull' => false));
        $teaser->addColumn('configuration', 'text', array('notnull' => false));
        $teaser->addColumn('inherit', 'boolean');
        $teaser->addColumn('no_display', 'boolean', array('default' => 0));
        $teaser->addColumn('disable_cache', 'boolean');
        $teaser->addColumn('cache_lifetime', 'integer', array('unsigned' => true, 'default' => 0));
        $teaser->addColumn('sort', 'integer', array('unsigned' => true, 'default' => 0));
        $teaser->addColumn('stop_inherit', 'boolean');
        $teaser->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $teaser->addColumn('created_at', 'datetime');
        $teaser->setPrimaryKey(array('id'));

        $teaserHash = $schema->createTable('teaser_hash');
        $teaserHash->addColumn('teaser_id', 'integer', array('unsigned' => true));
        $teaserHash->addColumn('version', 'integer', array('unsigned' => true));
        $teaserHash->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $teaserHash->addColumn('hash', 'string', array('length' => 32, 'fixed' => true));
        $teaserHash->addColumn('debug', 'text', array('notnull' => false));
        $teaserHash->setPrimaryKey(array('teaser_id', 'version', 'language'));

        $teaserHistory = $schema->createTable('teaser_history');
        $teaserHistory->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $teaserHistory->addColumn('teaser_id', 'integer', array('unsigned' => true));
        $teaserHistory->addColumn('eid', 'integer', array('unsigned' => true));
        $teaserHistory->addColumn('language', 'string', array('length' => 2, 'fixed' => true, 'notnull' => false));
        $teaserHistory->addColumn('version', 'integer', array('unsigned' => true, 'notnull' => false));
        $teaserHistory->addColumn('action', 'string', array('length' => 20));
        $teaserHistory->addColumn('comment', 'text', array('notnull' => false));
        $teaserHistory->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $teaserHistory->addColumn('created_at', 'datetime');
        $teaserHistory->setPrimaryKey(array('id'));

        $teaserOnline = $schema->createTable('teaser_online');
        $teaserOnline->addColumn('teaser_id', 'integer', array('unsigned' => true));
        $teaserOnline->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $teaserOnline->addColumn('version', 'integer', array('unsigned' => true));
        $teaserOnline->addColumn('publish_user_id', 'string', array('length' => 36, 'fixed' => true));
        $teaserOnline->addColumn('published_at', 'datetime');
        $teaserOnline->setPrimaryKey(array('teaser_id', 'language'));
        $teaserOnline->addForeignKeyConstraint('teaser', array('teaser_id'), array('id'));

        $teaserCatch = $schema->createTable('catch');
        $teaserCatch->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $teaserCatch->addColumn('title', 'string', array('length' => 255));
        $teaserCatch->addColumn('tree_id', 'integer', array('unsigned' => true));
        $teaserCatch->addColumn('elementtype_ids', 'text');
        $teaserCatch->addColumn('in_navigation', 'boolean');
        $teaserCatch->addColumn('max_depth', 'integer', array('unsigned' => true));
        $teaserCatch->addColumn('sort_field', 'string', array('length' => 255, 'notnull' => false));
        $teaserCatch->addColumn('sort_order', 'string', array('length' => 255));
        $teaserCatch->addColumn('filter', 'string', array('length' => 255, 'notnull' => false));
        $teaserCatch->addColumn('rotation', 'boolean');
        $teaserCatch->addColumn('paginator', 'boolean');
        $teaserCatch->addColumn('max_results', 'integer', array('unsigned' => true));
        $teaserCatch->addColumn('pool_size', 'integer', array('unsigned' => true));
        $teaserCatch->addColumn('results_per_page', 'integer', array('unsigned' => true));
        $teaserCatch->addColumn('template', 'string', array('length' => 255));
        $teaserCatch->addColumn('meta_search', 'string', array('length' => 255, 'notnull' => false));
        $teaserCatch->addColumn('create_user_id', 'string', array('length' => 36, 'fixed' => true));
        $teaserCatch->addColumn('created_at', 'datetime');
        $teaserCatch->setPrimaryKey(array('id'));

        $teaserCatchLookupElement = $schema->createTable('catch_lookup_element');
        $teaserCatchLookupElement->addColumn('eid', 'integer');
        $teaserCatchLookupElement->addColumn('version', 'integer', array('unsigned' => true));
        $teaserCatchLookupElement->addColumn('tree_id', 'integer', array('unsigned' => true));
        $teaserCatchLookupElement->addColumn('elementtype_id', 'integer', array('unsigned' => true));
        $teaserCatchLookupElement->addColumn('is_preview', 'boolean');
        $teaserCatchLookupElement->addColumn('in_navigation', 'boolean');
        $teaserCatchLookupElement->addColumn('is_restricted', 'boolean');
        $teaserCatchLookupElement->addColumn('published_at', 'datetime');
        $teaserCatchLookupElement->addColumn('custom_date', 'date', array('notnull' => false));
        $teaserCatchLookupElement->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $teaserCatchLookupElement->addColumn('online_version', 'integer', array('unsigned' => true, 'notnull' => false));
        $teaserCatchLookupElement->setPrimaryKey(array('eid', 'version', 'tree_id', 'is_preview', 'language'));

        $teaserCatchLookupMeta = $schema->createTable('catch_lookup_meta');
        $teaserCatchLookupMeta->addColumn('id', 'integer');
        $teaserCatchLookupMeta->addColumn('set_id', 'string', array('length' => 36, 'fixed' => true));
        $teaserCatchLookupMeta->addColumn('tree_id', 'integer', array('unsigned' => true));
        $teaserCatchLookupMeta->addColumn('eid', 'integer');
        $teaserCatchLookupMeta->addColumn('version', 'integer', array('unsigned' => true));
        $teaserCatchLookupMeta->addColumn('language', 'string', array('length' => 2, 'fixed' => true));
        $teaserCatchLookupMeta->addColumn('key', 'string', array('length' => 100));
        $teaserCatchLookupMeta->addColumn('value', 'string', array('length' => 255));
    }
}