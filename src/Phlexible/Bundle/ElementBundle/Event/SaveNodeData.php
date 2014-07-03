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
 * Save Node Data Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_SaveNodeData extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::SAVE_NODE_DATA;

    /**
     * @var Makeweb_Elements_Tree_Node
     */
    protected $_node = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var array
     */
    protected $_data = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Tree_Node $node
     * @param string                     $language
     * @param array                      $data
     */
    public function __construct(Makeweb_Elements_Tree_Node $node, $language, array $data)
    {
        $this->_node = $node;
        $this->_language = $language;
        $this->_data = $data;
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

    /**
     * Return data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
