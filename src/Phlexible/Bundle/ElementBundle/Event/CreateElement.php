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
 * Create Element Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_CreateElement extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::CREATE_ELEMENT;

    /**
     * @var Makeweb_Elements_Element
     */
    protected $_element = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Element $element
     */
    public function __construct(Makeweb_Elements_Element $element)
    {
        $this->_element = $element;
    }

    /**
     * Return element
     *
     * @return Makeweb_Elements_Element
     */
    public function getElement()
    {
        return $this->_element;
    }
}