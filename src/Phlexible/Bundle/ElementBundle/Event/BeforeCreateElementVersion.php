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
 * Before Create Element Version Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeCreateElementVersion extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_CREATE_ELEMENT_VERSION;

    /**
     * @var int
     */
    protected $_eid = null;

    /**
     * @var int
     */
    protected $_newVersion = null;

    /**
     * Constructor
     *
     * @param int $eid
     * @param int $newVersion
     */
    public function __construct($eid, $newVersion)
    {
        $this->_eid = $eid;
        $this->_newVersion = $newVersion;
    }

    /**
     * Return eid
     *
     * @return int
     */
    public function getEid()
    {
        return $this->_eid;
    }

    /**
     * Return new version
     *
     * @return int
     */
    public function getNewVersion()
    {
        return $this->_newVersion;
    }
}