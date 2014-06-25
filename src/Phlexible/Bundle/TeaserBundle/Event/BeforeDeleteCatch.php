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
 * Before Delete Catch Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeDeleteCatch extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_DELETE_CATCH;

    /**
     * @var integer
     */
    protected $_catchId = null;

    /**
     * Constructor
     *
     * @param integer $catchId
     */
    public function __construct($catchId)
    {
        $this->_catchId = $catchId;
    }

    /**
     * Return Catch ID
     *
     * @return integer
     */
    public function getCatchId()
    {
        return $this->_catchId;
    }
}
