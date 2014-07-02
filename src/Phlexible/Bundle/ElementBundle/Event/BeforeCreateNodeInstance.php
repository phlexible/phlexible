<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Before Create Node Instance Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeCreateNodeInstance extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_CREATE_NODE_INSTANCE;

    /**
     * @var Makeweb_Elements_Tree
     */
    protected $_tree = null;

    /**
     * @var int
     */
    protected $_targetId;

    /**
     * @var int
     */
    protected $_instanceId;

    /**
     * @var int
     */
    protected $_prevId;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Tree $tree
     * @param int                   $targetId
     * @param int                   $instanceId
     * @param int                   $prevId
     */
    public function __construct(Makeweb_Elements_Tree $tree, $targetId, $instanceId, $prevId)
    {
        $this->_tree       = $tree;
        $this->_targetId   = $targetId;
        $this->_instanceId = $instanceId;
        $this->_prevId     = $prevId;
    }

    /**
     * Return tree
     *
     * @return Makeweb_Elements_Tree
     */
    public function getTree()
    {
        return $this->_node;
    }

    /**
     * Return target ID
     *
     * @return int
     */
    public function getTargetId()
    {
        return $this->_targetId;
    }

    /**
     * Return instance ID
     *
     * @return int
     */
    public function getInstanceId()
    {
        return $this->_instanceId;
    }

    /**
     * Return previous ID
     *
     * @return int
     */
    public function getPrevId()
    {
        return $this->_version;
    }
}