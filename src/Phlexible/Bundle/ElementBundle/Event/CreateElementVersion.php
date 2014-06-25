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
 * Create Element Version Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_CreateElementVersion extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::CREATE_ELEMENT_VERSION;

    /**
     * @var Makeweb_Elements_Element_Version
     */
    protected $_elementVersion = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Element_Version $elementVersion
     */
    public function __construct(Makeweb_Elements_Element_Version $elementVersion)
    {
        $this->_elementVersion = $elementVersion;
    }

    /**
     * Return element version
     *
     * @return Makeweb_Elements_Element_Version
     */
    public function getElementVersion()
    {
        return $this->_elementVersion;
    }
}