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
 * Save Element Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_SaveElement extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::SAVE_ELEMENT;

    /**
     * @var Makeweb_Elements_Element_Version
     */
    protected $_elementVersion = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var integer
     */
    protected $_oldVersion = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Element_Version $elementVersion
     * @param string                           $language
     * @param integer                          $oldVersion
     */
    public function __construct(Makeweb_Elements_Element_Version $elementVersion, $language, $oldVersion)
    {
        $this->_elementVersion = $elementVersion;
        $this->_language = $language;
        $this->_oldVersion = $oldVersion;
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

    /**
     * Return language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Return old version
     *
     * @return integer
     */
    public function getOldVersion()
    {
        return $this->_oldVersion;
    }
}