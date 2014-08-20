<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch;

use Doctrine\DBAL\Connection;

/**
 * Catch helper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CatchHelper
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Makeweb_Elements_Element_Manager
     */
    protected $_elementManager = null;

    /**
     * @var Makeweb_Elements_Element_Version_Manager
     */
    protected $_elementVersionManager = null;

    /**
     * @param Connection                               $connection
     * @param Makeweb_Elements_Element_Manager         $elementManager
     * @param Makeweb_Elements_Element_Version_Manager $elementVersionManager
     */
    public function __construct(
        Connection $connection,
        Makeweb_Elements_Element_Manager $elementManager,
        Makeweb_Elements_Element_Version_Manager $elementVersionManager)
    {
        $this->connection = $connection;
        $this->_elementManager = $elementManager;
        $this->_elementVersionManager = $elementVersionManager;
    }

    public function removeByTid($tid)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'tid = ?' => $tid
            )
        );

        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_metaset_items',
            array(
                'tid = ?' => $tid
            )
        );
    }

    public function removeByEid($eid)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'eid = ?' => $eid
            )
        );

        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_metaset_items',
            array(
                'eid = ?' => $eid
            )
        );
    }

    public function removePreviewByTid($tid)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'tid = ?'     => $tid,
                'preview = ?' => 1,
            )
        );
    }

    public function removePreviewByEid($eid)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'eid = ?'     => $eid,
                'preview = ?' => 1,
            )
        );
    }

    public function removeOnlineByTid($tid)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'tid = ?'     => $tid,
                'preview = ?' => 0,
            )
        );
    }

    public function removeOnlineByEid($eid)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'eid = ?'     => $eid,
                'preview = ?' => 0,
            )
        );
    }

    public function removeOnlineByTidAndLanguage($tid, $language)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'tid = ?'      => $tid,
                'language = ?' => $language,
                'preview = ?'  => 0,
            )
        );
    }

    public function removeOnlineByEidAndLanguage($eid, $language)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_helper',
            array(
                'eid = ?'      => $eid,
                'language = ?' => $language,
                'preview = ?'  => 0,
            )
        );
    }

    public function removeMetaByTidAndVersionAndLanguage($tid, $version, $language)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_metaset_items',
            array(
                'tid = ?'      => $tid,
                'version = ?'  => $version,
                'language = ?' => $language,
            )
        );
    }

    public function removeMetaByEidAndVersionAndLanguage($eid, $version, $language)
    {
        // Delete old entries
        $this->_db->delete(
            $this->_db->prefix . 'catchteaser_metaset_items',
            array(
                'eid = ?'      => $eid,
                'version = ?'  => $version,
                'language = ?' => $language,
            )
        );
    }

    public function update($eid)
    {
        $this->updateOnline($eid);
        $this->updatePreview($eid);
    }

    public function updatePreview($eid)
    {
        $dispatcher = Brainbits_Event_Dispatcher::getInstance();

        $beforeEvent = new Makeweb_Teasers_Event_BeforeUpdateCatchTeaserHelper($eid, true);
        if (false === $dispatcher->dispatch($beforeEvent)) {
            return null;
        }

        $this->removePreviewByEid($eid);

        $element = $this->_elementManager->getByEID($eid);

        // Preview version
        $select = $this->_db->select()
            ->distinct()
            ->from(
                array('et' => $this->_db->prefix . 'element_tree'),
                array('eid', 'id', 'modify_time')
            )
            ->join(
                array('e' => $this->_db->prefix . 'element'),
                'et.eid = e.eid',
                array('latest_version')
            )
            ->join(
                array('ed' => $this->_db->prefix . 'element_data'),
                'et.eid = ed.eid',
                array()
            )
            ->join(
                array('edl' => $this->_db->prefix . 'element_data_language'),
                'ed.eid = edl.eid' .
                ' AND ed.data_id = edl.data_id' .
                ' AND ed.version = edl.version' .
                ' AND edl.version = e.latest_version',
                array('language')
            )
            ->join(
                array('etp' => $this->_db->prefix . 'element_tree_page'),
                'et.id = etp.tree_id AND ' .
                'etp.version = edl.version',
                array('navigation', 'restricted')
            )
            ->where('et.eid = ?', $eid);

        $nodes = $this->_db->fetchAll($select);

        foreach ($nodes as $node) {
            $elementVersion = $this->_elementVersionManager->get($eid, $node['latest_version']);

            $this->_updateVersion(
                $element,
                $elementVersion,
                $node['id'],
                $node['modify_time'],
                true,
                $node['language'],
                $node['navigation'],
                $node['restricted'],
                null //#$node['online_version']
            );
        }

        $event = new Makeweb_Teasers_Event_UpdateCatchTeaserHelper($eid, true);
        $dispatcher->dispatch($event);

        return count($nodes);
    }

    public function updateOnline($eid)
    {
        $dispatcher = Brainbits_Event_Dispatcher::getInstance();

        $beforeEvent = new Makeweb_Teasers_Event_BeforeUpdateCatchTeaserHelper($eid);
        if (false === $dispatcher->dispatch($beforeEvent)) {
            return null;
        }

        $this->removeOnlineByEid($eid);

        $element = $this->_elementManager->getByEID($eid);

        // Live version
        $select = $this->_db->select()
            ->from(
                array('eto' => $this->_db->prefix . 'element_tree_online'),
                array('tree_id', 'version', 'publish_time', 'language')
            )
            ->join(
                array('etp' => $this->_db->prefix . 'element_tree_page'),
                'eto.tree_id = etp.tree_id AND ' .
                'eto.version = etp.version',
                array('restricted', 'navigation')
            )
            ->where('eto.eid = ?', $eid);

        $nodes = $this->_db->fetchAll($select);

        foreach ($nodes as $node) {
            $elementVersion = $this->_elementVersionManager->get($eid, $node['version']);

            $this->_updateVersion(
                $element,
                $elementVersion,
                $node['tree_id'],
                $node['publish_time'],
                false,
                $node['language'],
                $node['navigation'],
                $node['restricted'],
                null
            );
        }

        $event = new Makeweb_Teasers_Event_UpdateCatchTeaserHelper($eid);
        $dispatcher->dispatch($event);

        return count($nodes);
    }

    protected function _updateVersion(
        Makeweb_Elements_Element $element,
        Makeweb_Elements_Element_Version $elementVersion,
        $tid,
        $time,
        $preview,
        $language,
        $navigation,
        $restricted,
        $onlineVersion)
    {
        $eid = $element->getEid();
        $version = $elementVersion->getVersion();
        $customDate = $elementVersion->getCustomDate($language);

        // fill catchteaser_metaset_itmes

        $this->_updateMeta($tid, $eid, $version, $language);

        // fill catchteaser_helper

        $insertData = array(
            'eid'            => $eid,
            'tid'            => $tid,
            'publish_time'   => $time,
            'custom_date'    => $customDate,
            'preview'        => $preview ? 1 : 0,
            'elementtype_id' => $element->getElementTypeId(),
            'version'        => $version,
            'language'       => $language,
            'online_version' => $onlineVersion,
            'in_navigation'  => (int) $navigation,
            'restricted'     => (int) $restricted,
        );

        $this->_db->insert($this->_db->prefix . 'catchteaser_helper', $insertData);
    }

    protected function _updateMeta($tid, $eid, $version, $language)
    {
        $selectMetasetItems = $this->_db->select()
            ->from($this->_db->prefix . 'element_version_metaset_items')
            ->where('eid = ?', $eid)
            ->where('version = ?', $version)
            ->where('language = ?', $language)
            ->where('value IS NOT NULL')
            ->where('value != ""');

        $metasetItems = $this->_db->fetchAll($selectMetasetItems);

        $this->removeMetaByEidAndVersionAndLanguage($eid, $version, $language);

        foreach ($metasetItems as $metaset) {
            $cleanString = str_replace(
                array(',', ';'),
                array('===', '==='),
                html_entity_decode($metaset['value'], ENT_COMPAT, 'UTF-8')
            );

            $tmp = explode('===', $cleanString);

            foreach ($tmp as $item) {
                $metaset['tid'] = $tid;
                $metaset['value'] = mb_strtolower(trim($item));
                $this->_db->insert($this->_db->prefix . 'catchteaser_metaset_items', $metaset);
            }
        }
    }
}
