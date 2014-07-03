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
 * @author      Peter Fahsel <pfahsel@brainbits.net>
 * @copyright   2013 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeCacheElementVersionCustomTitles extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_CACHE_ELEMENT_VERSION_CUSTOM_TITLES;

    /**
     * @var Makeweb_Elements_Element_Version
     */
    protected $_elementVersion;

    /**
     * @var string
     */
    protected $_language;

    /**
     * @var Makeweb_Elements_Element_Version_CustomTitles
     */
    protected $_customTitles;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Element_Version              $elementVersion
     * @param string                                        $language
     * @param Makeweb_Elements_Element_Version_CustomTitles $customTitles
     */
    public function __construct(
        Makeweb_Elements_Element_Version $elementVersion,
        $language,
        Makeweb_Elements_Element_Version_CustomTitles $customTitles
    )
    {
        $this->_elementVersion = $elementVersion;
        $this->_language = $language;
        $this->_customTitles = $customTitles;
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
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @return \Makeweb_Elements_Element_Version_CustomTitles
     */
    public function getCustomTitles()
    {
        return $this->_customTitles;
    }
}