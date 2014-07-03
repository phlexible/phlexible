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
 * Before Create Element Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeCreateElement extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_CREATE_ELEMENT;

    /**
     * @var int
     */
    protected $_elementTypeId = null;

    /**
     * Constructor
     *
     * @param int $elementTypeId
     */
    public function __construct($elementTypeId)
    {
        $this->_elementTypeId = $elementTypeId;
    }

    /**
     * Return element type id
     *
     * @return int
     */
    public function getElementTypeId()
    {
        return $this->_elementTypeId;
    }
}