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
 * Before Save Element Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeSaveElement extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_SAVE_ELEMENT;

    /**
     * @var Makeweb_Elements_Element
     */
    protected $_element = null;

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
     * @param Makeweb_Elements_Element $element
     * @param string                   $language
     * @param integer                  $oldVersion
     */
    public function __construct(Makeweb_Elements_Element $element, $language, $oldVersion)
    {
        $this->_element = $element;
        $this->_language = $language;
        $this->_oldVersion = $oldVersion;
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