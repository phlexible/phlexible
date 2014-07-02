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
 * Before Save Element Data Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Event_BeforeSaveElementData extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::BEFORE_SAVE_ELEMENT_DATA;

    /**
     * @var Makeweb_Elements_Element_Version
     */
    protected $_elementVersion = null;

    /**
     * @var param
     */
    protected $_values = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var int
     */
    protected $_oldVersion = null;

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Element_Version $elementVersion
     * @param array                            $values
     * @param string                           $language
     * @param int                              $oldVersion
     */
    public function __construct(Makeweb_Elements_Element_Version $elementVersion, array $values, $language, $oldVersion)
    {
        $this->_elementVersion = $elementVersion;
        $this->_values = $values;
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
     * Return values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
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
     * @return int
     */
    public function getOldVersion()
    {
        return $this->_oldVersion;
    }
}