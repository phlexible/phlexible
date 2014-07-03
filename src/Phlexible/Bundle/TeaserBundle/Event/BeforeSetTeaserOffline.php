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
 * Before Set Teaser Offline Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeSetTeaserOffline extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_SET_TEASER_OFFLINE;

    /**
     * @var Makeweb_Teasers_Node
     */
    protected $_node = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * Constructor
     *
     * @param Makeweb_Teasers_Node $node
     * @param string               $language
     */
    public function __construct(Makeweb_Teasers_Node $node, $language)
    {
        $this->_node = $node;
        $this->_language = $language;
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
}
