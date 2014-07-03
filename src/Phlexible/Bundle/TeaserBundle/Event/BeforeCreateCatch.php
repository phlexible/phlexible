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
 * Before Create Catch Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeCreateCatch extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_CREATE_CATCH;

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
    protected $_layoutAreaId = null;

    /**
     * Constructor
     *
     * @param int $treeId
     * @param int $eid
     * @param int $layoutAreaId
     */
    public function __construct($treeId, $eid, $layoutAreaId)
    {
        $this->_treeId = $treeId;
        $this->_eid = $eid;
        $this->_layoutAreaId = $layoutAreaId;
    }

    public function getTreeId()
    {
        return $this->_treeId;
    }

    public function getEid()
    {
        return $this->_eid;
    }

    public function getLayourAreaId()
    {
        return $this->_layoutAreaId;
    }
}
