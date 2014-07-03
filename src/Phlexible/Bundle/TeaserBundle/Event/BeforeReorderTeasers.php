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
 * Before Reorder Teasers Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Peter Fahsel <pfahsel@brainbits.net>
 * @copyright   2012 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeReorderTeasers extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_REORDER_TEASERS;

    /**
     * @var string
     */
    protected $_treeId = null;

    /**
     * @var string
     */
    protected $_eid = null;

    /**
     * @var string
     */
    protected $_areaId = null;

    /**
     * @var array
     */
    protected $_sortIds = null;


    /**
     * Constructor
     *
     * @param       $treeId
     * @param       $eid
     * @param       $areaId
     * @param array $sortIds
     */
    public function __construct($treeId, $eid, $areaId, array $sortIds = array())
    {
        $this->_treeId = $treeId;
        $this->_eid = $eid;
        $this->_areaId = $areaId;
        $this->_sortIds = $sortIds;
    }

    /**
     * @return string
     */
    public function getAreaId()
    {
        return $this->_areaId;
    }

    /**
     * @return string
     */
    public function getEid()
    {
        return $this->_eid;
    }

    /**
     * @return array
     */
    public function getSortIds()
    {
        return $this->_sortIds;
    }

    /**
     * @return string
     */
    public function getTreeId()
    {
        return $this->_treeId;
    }
}
