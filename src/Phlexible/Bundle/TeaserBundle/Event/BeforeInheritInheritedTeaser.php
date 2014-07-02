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
 * Before Inherit Inherited Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeInheritInheritedTeaser extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_INHERIT_INHERITED_TEASER;

    /**
     * @var int
     */
    protected $_treeId = null;

    /**
     * @var int
     */
    protected $_eid = null;

    /**
     * @var int
     */
    protected $_teaserEid = null;

    /**
     * @var int
     */
    protected $_layoutarreaId = null;

    /**
     * Constructor
     *
     * @param int $treeId
     * @param int $eid
     * @param int $teaserEid
     * @param int $layoutarreaId
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
     * @return int
     */
    public function getTreeId()
    {
        return $this->_treeId;
    }

    /**
     * Return EID
     *
     * @return int
     */
    public function getEid()
    {
        return $this->_eid;
    }

    /**
     * Return teaser EID
     *
     * @return int
     */
    public function getTeaserEid()
    {
        return $this->_teaserEid;
    }

    /**
     * Return layoutarea ID
     *
     * @return int
     */
    public function getLayoutareaId()
    {
        return $this->_layoutarreaId;
    }
}
