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
 * Before Show Inherited Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeShowInheritedTeaser extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_SHOW_INHERITED_TEASER;

    /**
     * @var integer
     */
    protected $_treeId = null;

    /**
     * @var integer
     */
    protected $_eid = null;

    /**
     * @var integer
     */
    protected $_teaserEid = null;

    /**
     * @var integer
     */
    protected $_layoutarreaId = null;

    /**
     * Constructor
     *
     * @param integer $treeId
     * @param integer $teaserEid
     * @param ineger  $layoutarreaId
     */
    public function __construct($treeId, $eid, $teaserEid, $layoutarreaId)
    {
        $this->_treeId = $treeId;
        $this->_eid = $eid;
        $this->_teaserEid = $teaserEid;
        $this->_layoutarreaId = $layoutarreaId;
    }

    /**
     * Return tree ID
     *
     * @return integer
     */
    public function getTreeId()
    {
        return $this->_treeId;
    }

    /**
     * Return EID
     *
     * @return integer
     */
    public function getEid()
    {
        return $this->_eid;
    }

    /**
     * Return teaser EID
     *
     * @return integer
     */
    public function getTeaserEid()
    {
        return $this->_teaserEid;
    }

    /**
     * Return layoutarea ID
     *
     * @return integer
     */
    public function getLayoutareaId()
    {
        return $this->_layoutarreaId;
    }
}
