<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Component\Database\ConnectionManager;

/**
 * Tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeSorter
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var string
     */
    protected $_sortLang = null;

    /**
     * @param ConnectionManager $dbPool
     * @param string            $sortLang
     */
    public function __construct(ConnectionManager $dbPool, $sortLang)
    {
        $this->_db = $dbPool->write;
        $this->_sortLang = $sortLang;
    }

    /**
     * Return meta
     *
     * @param Tree $tree
     *
     * @return array
     */
    public function sortTree(Tree $tree)
    {
        $rii = new \RecursiveIteratorIterator($tree->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($rii as $node) {
            /* @var $node TreeNode */

            if (!$node->hasChildren()) {
                continue;
            }

            $sortedChildren = $this->_getSortedChildren($node);

            foreach ($sortedChildren as $sort => $tid) {
                $sort++;
                $this->_db->update(
                    $this->_db->prefix . 'element_tree',
                    array('sort' => $sort),
                    array('id = ?' => $tid)
                );
            }
        }

        $tree->_getMeta(true);
    }

    /**
     * @param TreeNode $node
     */
    public function sortNode(TreeNode $node)
    {
        if (!$node->hasChildren()) {
            return;
        }

        $sortedChildren = $this->_getSortedChildren($node);

        foreach ($sortedChildren as $sort => $tid) {
            $sort++;
            $this->_db->update(
                $this->_db->prefix . 'element_tree',
                array('sort' => $sort),
                array('id = ?' => $tid)
            );
        }

        $node->getTree()->_getMeta(true);
    }

    protected function _getSortedChildren(TreeNode $node)
    {
        $db = $this->_db;
        $sortLang = $this->_sortLang;

        $parentId = $node->getId();
        $sortMode = $node->getSortMode();
        $sortDir = $node->getSortDir();

        $select = $db->select()
            ->from(
                array('et' => $db->prefix . 'element_tree'),
                array(
                    'id'
                )
            )
            ->where('et.parent_id = ?', $parentId);

        switch ($sortMode) {
            case TreeInterface::SORT_MODE_TITLE:
                $select
                    ->joinLeft(
                        array('e' => $db->prefix . 'element'),
                        'et.eid = e.eid',
                        array()
                    )
                    ->joinLeft(
                        array('evt' => $db->prefix . 'element_version_titles'),
                        'e.eid = evt.eid AND e.latest_version = evt.version AND evt.language = ' . $db->quote(
                            $sortLang
                        ),
                        array()
                    )
                    ->order('evt.backend ' . $sortDir);
                break;

            case TreeInterface::SORT_MODE_CREATEDATE:
                $select->order('et.modify_time ' . $sortDir);
                break;

            case TreeInterface::SORT_MODE_PUBLISHDATE:
                $select
                    ->joinLeft(
                        array('eto' => $db->prefix . 'element_tree_online'),
                        'eto.tree_id = et.id AND eto.language = ' . $db->quote($sortLang),
                        array()
                    )
                    ->order('eto.publish_time ' . $sortDir);
                break;

            case TreeInterface::SORT_MODE_CUSTOMDATE:
                $select
                    ->joinLeft(
                        array('e' => $db->prefix . 'element'),
                        'et.eid = e.eid',
                        array()
                    )
                    ->joinLeft(
                        array('evt' => $db->prefix . 'element_version_titles'),
                        'e.eid = evt.eid AND e.latest_version = evt.version AND evt.language = ' . $db->quote(
                            $sortLang
                        ),
                        array()
                    )
                    ->order('evt.date ' . $sortDir);
                break;

            default:
                $select->order('et.sort ASC');
                break;
        }

        $result = $db->fetchCol($select);

        return $result;
    }
}
