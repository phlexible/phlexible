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
 * Before Publish Node Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforePublishNode extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_PUBLISH_NODE;

    /**
     * @var Makeweb_Elements_Tree_Node
     */
    protected $_node = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var int
     */
    protected $_version = null;

    /**
     * @var bool
     */
    protected $_isRecursive = false;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Tree_Node $node
     * @param string                     $language
     * @param int                        $version
     * @param bool                       $isRecursive
     */
    public function __construct(Makeweb_Elements_Tree_Node $node, $language, $version, $isRecursive = false)
    {
        $this->_node        = $node;
        $this->_language    = $language;
        $this->_version     = $version;
        $this->_isRecursive = (bool) $isRecursive;
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
     * Return version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Is this a recusrive publish?
     *
     * @return bool
     */
    public function isRecursive()
    {
        return $this->_isRecursive;
    }
}
