<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\TreeBundle\Doctrine\Tree;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;

/**
 * Tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeSorter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    protected $_sortLang;

    /**
     * @param Connection $connection
     * @param string     $sortLang
     */
    public function __construct(Connection $connection, $sortLang)
    {
        $this->connection = $connection;
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

            $sortedChildren = $this->getSortedChildren($node);

            foreach ($sortedChildren as $sort => $tid) {
                $sort++;
                $this->_db->update(
                    $this->_db->prefix . 'element_tree',
                    ['sort' => $sort],
                    ['id = ?' => $tid]
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

        $sortedChildren = $this->getSortedChildren($node);

        foreach ($sortedChildren as $sort => $tid) {
            $sort++;
            $this->_db->update(
                $this->_db->prefix . 'element_tree',
                ['sort' => $sort],
                ['id = ?' => $tid]
            );
        }

        $node->getTree()->_getMeta(true);
    }

    protected function getSortedChildren(TreeNode $node)
    {
        $db = $this->_db;
        $sortLang = $this->_sortLang;

        $parentId = $node->getId();
        $sortMode = $node->getSortMode();
        $sortDir = $node->getSortDir();

        $select = $db->select()
            ->from(
                ['et' => $db->prefix . 'element_tree'],
                [
                    'id'
                ]
            )
            ->where('et.parent_id = ?', $parentId);

        switch ($sortMode) {
            case TreeInterface::SORT_MODE_TITLE:
                $select
                    ->joinLeft(
                        ['e' => $db->prefix . 'element'],
                        'et.eid = e.eid',
                        []
                    )
                    ->joinLeft(
                        ['evt' => $db->prefix . 'element_version_titles'],
                        'e.eid = evt.eid AND e.latest_version = evt.version AND evt.language = ' . $db->quote(
                            $sortLang
                        ),
                        []
                    )
                    ->order('evt.backend ' . $sortDir);
                break;

            case TreeInterface::SORT_MODE_CREATEDATE:
                $select->order('et.modify_time ' . $sortDir);
                break;

            case TreeInterface::SORT_MODE_PUBLISHDATE:
                $select
                    ->joinLeft(
                        ['eto' => $db->prefix . 'element_tree_online'],
                        'eto.tree_id = et.id AND eto.language = ' . $db->quote($sortLang),
                        []
                    )
                    ->order('eto.publish_time ' . $sortDir);
                break;

            case TreeInterface::SORT_MODE_CUSTOMDATE:
                $select
                    ->joinLeft(
                        ['e' => $db->prefix . 'element'],
                        'et.eid = e.eid',
                        []
                    )
                    ->joinLeft(
                        ['evt' => $db->prefix . 'element_version_titles'],
                        'e.eid = evt.eid AND e.latest_version = evt.version AND evt.language = ' . $db->quote(
                            $sortLang
                        ),
                        []
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
