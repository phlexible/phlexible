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
 * Before Set Node Offline Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeSetNodeOffline extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_SET_NODE_OFFLINE;

    /**
     * @var Makeweb_Elements_Tree_Node
     */
    protected $_node = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Tree_Node $node
     * @param string                     $language
     */
    public function __construct(Makeweb_Elements_Tree_Node $node, $language)
    {
        $this->_node     = $node;
        $this->_language = $language;
    }

    /**
     * Return node
     *
     * @return Makeweb_Elements_Tree_Node
     */
    public function getNode()
    {
        return $this->_node;
    }

    /**
     * Return language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }
}
