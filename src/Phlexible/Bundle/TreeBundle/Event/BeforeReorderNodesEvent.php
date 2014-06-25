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
 * Before Reorder Nodes Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeReorderNodes extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_REORDER_NODES;

    /**
     * @var Makeweb_Elements_Tree_Node
     */
    protected $_parentNode = null;

    /**
     * @var array
     */
    protected $_sortIds = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Tree_Node $parentNode
     */
    public function __construct(Makeweb_Elements_Tree_Node $parentNode, array $sortIds = array())
    {
        $this->_parentNode = $parentNode;
        $this->_sortIds    = $sortIds;
    }

    /**
     * Return parent node
     *
     * @return Makeweb_Elements_Tree_Node
     */
    public function getParentNode()
    {
        return $this->_parentNode;
    }

    /**
     * Return sort IDs
     *
     * @return array
     */
    public function getSortIds()
    {
        return $this->_sortIds;
    }
}
