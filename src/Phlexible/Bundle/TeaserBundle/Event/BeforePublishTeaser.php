<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Before Publish Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforePublishTeaser extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_PUBLISH_TEASER;

    /**
     * @var Makeweb_Teasers_Node
     */
    protected $_node = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var integer
     */
    protected $_version = null;

    /**
     * Constructor
     *
     * @param Makeweb_Teasers_Node $node
     * @param string               $language
     * @param integer              $version
     */
    public function __construct(Makeweb_Teasers_Node $node, $language, $version)
    {
        $this->_node     = $node;
        $this->_language = $language;
        $this->_version  = $version;
    }

    /**
     * Return node
     *
     * @return Makeweb_Teasers_Node
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
     * @return integer
     */
    public function getVersion()
    {
        return $this->_version;
    }
}
