<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;

/**
 * Tree history
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeHistory
{
    const ACTION_CREATE_NODE     = 'createNode';
    const ACTION_DELETE_NODE     = 'deleteNode';
    const ACTION_MOVE_NODE       = 'moveNode';
    const ACTION_CREATE_INSTANCE = 'createInstance';
    const ACTION_PUBLISH         = 'publish';
    const ACTION_SET_OFFLINE     = 'setOffline';

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @param ConnectionManager $dbPool
     */
    public function __construct(ConnectionManager $dbPool)
    {
        $this->db = $dbPool->default;
    }

    /**
     * Insert create node history entry
     *
     * @param TreeNodeInterface $node
     * @param string            $uid
     * @param integer           $eid
     * @param integer           $version
     * @param string            $language
     * @param string            $comment
     */
    public function insertCreateNode(TreeNodeInterface $node, $uid, $eid, $version = null, $language = null, $comment = null)
    {
        $this->insert(self::ACTION_CREATE_NODE, $node, $uid, $eid, $version, $language, $comment);
    }

    /**
     * Insert move node history entry
     *
     * @param TreeNodeInterface $node
     * @param string            $uid
     * @param integer           $eid
     * @param integer           $version
     * @param string            $language
     * @param string            $comment
     */
    public function insertMoveNode(TreeNodeInterface $node, $uid, $eid, $version = null, $language = null, $comment = null)
    {
        $this->insert(self::ACTION_MOVE_NODE, $node, $uid, $eid, $version, $language, $comment);
    }

    /**
     * Insert delete node history entry
     *
     * @param TreeNodeInterface $node
     * @param string            $uid
     * @param integer           $eid
     * @param integer           $version
     * @param string            $language
     * @param string            $comment
     */
    public function insertDeleteNode(TreeNodeInterface $node, $uid, $eid, $version = null, $language = null, $comment = null)
    {
        $this->insert(self::ACTION_DELETE_NODE, $node, $uid, $eid, $version, $language, $comment);
    }

    /**
     * Insert history entry
     *
     * @param string            $action
     * @param TreeNodeInterface $node
     * @param string            $uid
     * @param integer           $eid
     * @param string            $version
     * @param string            $language
     * @param string            $comment
     */
    public function insert($action, TreeNodeInterface $node, $uid, $eid, $version = null, $language = null, $comment = null)
    {
        $this->db->insert(
            $this->db->prefix . 'tree_history',
            array(
                'tid'         => $node->getId(),
                'eid'         => $eid,
                'language'    => $language,
                'version'     => $version,
                'action'      => $action,
                'comment'     => $comment,
                'create_time' => $this->db->fn->now(),
                'create_uid'  => $uid,
            )
        );
    }
}
