<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

/**
 * Before reorder teasers event
 *
 * @author Peter Fahsel <pfahsel@brainbits.net>
 */
class BeforeReorderTeasersEvent extends \Symfony\Component\EventDispatcher\Event
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
     * @param int   $treeId
     * @param int   $eid
     * @param int   $areaId
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
