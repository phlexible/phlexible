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
 * Load Node Data Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_LoadNodeData extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::LOAD_NODE_DATA;

    /**
     * @var Makeweb_Elements_Tree_Node_Interface
     */
    protected $_node = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var object
     */
    protected $_data = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Tree_Node_Interface $node
     * @param string                               $language
     * @param object                               $data
     */
    public function __construct(Makeweb_Elements_Tree_Node_Interface $node, $language, $data)
    {
        $this->_node = $node;
        $this->_language = $language;
        $this->_data = $data;
    }

    /**
     * Return node
     *
     * @return Makeweb_Elements_Tree_Node_Interface
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

    /**
     * Return data
     *
     * @return object
     */
    public function getData()
    {
        return $this->_data;
    }
}
