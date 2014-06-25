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
 * Before Update Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeUpdateTeaser extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_UPDATE_TEASER;

    /**
     * @var integer
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
     * @param Makeweb_Teasers_Node $node
     * @param string               $language
     * @param array                $data
     */
    public function __construct(Makeweb_Teasers_Node $node, $language, array $data)
    {
        $this->_node     = $node;
        $this->_language = $language;
        $this->_data     = $data;
    }

    /**
     * Return teaser node
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
     * Return data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
