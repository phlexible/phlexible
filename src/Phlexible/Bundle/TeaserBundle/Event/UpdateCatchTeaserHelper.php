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
 * @author      Matthias Harmuth <sw@brainbits.net>
 * @copyright   2011 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_UpdateCatchTeaserHelper extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::UPDATE_CATCH_TEASER_HELPER;

    /**
     * @var int
     */
    protected $_eid = null;

    /**
     * @var bool
     */
    protected $_preview;

    /**
     * @param int  $eid
     * @param bool $preview
     */
    public function __construct($eid, $preview = false)
    {
        $this->_eid      = $eid;
        $this->_preview  = $preview;
    }

    public function getEid()
    {
        return $this->_eid;
    }

    public function isPreview()
    {
        return $this->_preview;
    }
}
