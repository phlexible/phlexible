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
 * Before Update Catch Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeUpdateCatch extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_UPDATE_CATCH;

    /**
     * @var int
     */
    protected $_catchId = null;

    /**
     * Constructor
     *
     * @param int $catchId
     */
    public function __construct($catchId)
    {
        $this->_catchId = $catchId;
    }

    /**
     * Return catch ID (teaser id)
     *
     * @return int
     */
    public function getCatchId()
    {
        return $this->_catchId;
    }
}
