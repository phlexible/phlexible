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
 * Before Create Teaser Instance Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeCreateTeaserInstance extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_CREATE_TEASER_INSTANCE;

    /**
     * @var int
     */
    protected $_treeId = null;

    /**
     * @var int
     */
    protected $_teaserId = null;

    /**
     * @var int
     */
    protected $_layoutAreaId = null;

    /**
     * Constructor
     *
     * @param int $treeId
     * @param int $teaserId
     * @param int $layoutAreaId
     */
    public function __construct($treeId, $teaserId, $layoutAreaId)
    {
        $this->_treeId = $treeId;
        $this->_teaserId = $teaserId;
        $this->_layoutAreaId = $layoutAreaId;
    }

    public function getTreeId()
    {
        return $this->_treeId;
    }

    public function getTeaserId()
    {
        return $this->_teaserId;
    }

    public function getLayourAreaId()
    {
        return $this->_layoutAreaId;
    }
}
